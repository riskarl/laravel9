<?php

namespace App\Http\Controllers;

use App\Models\Rab;
use Illuminate\Http\Request;
use App\Models\LPJ;
use App\Models\Proposal;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Models\User;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('files');
        $currentUser = $this->getCurrentUser();
        $jabatanId = $currentUser['code_jabatan'];
        $jabatan = $currentUser['jabatan'];
        $organisasi = $currentUser['organisasi'];

        // Membuat direktori jika tidak ada
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Handle file baru
        if ($request->hasFile('file')) {
            // Pindahkan file baru ke direktori
            $file->move($directory, $filename);
        }

        // Cek apakah ini update atau penambahan baru
        if (!empty($request->existing_file_name)) {
            // Ini adalah update file, temukan proposal yang ada
            $proposal = Proposal::where('id_proker', $request->id_proker)->first();
            if ($proposal && File::exists($directory . '/' . $proposal->file_proposal)) {
                // Hapus file lama
                File::delete($directory . '/' . $proposal->file_proposal);
            }
            $proposal->file_proposal = $filename;
            $proposal->status_flow = 0;
            $proposal->status = 'Pending';
            $proposal->catatan = 'Belum ada catatan';
        } else {
            // Ini adalah penambahan baru
            $proposal = new Proposal();
            $proposal->file_proposal = $filename;
            $proposal->status = 'Pending';
            $proposal->catatan = 'Belum ada catatan';
            $proposal->id_proker = $request->id_proker;
            $proposal->status_flow = 0;
        }

        // Simpan perubahan atau penambahan baru
        $proposal->save();

        $proker = Proker::where('id', $request->id_proker)->first();
        if (!$proker) {
            return redirect()->back()->with('error', 'Proker not found');
        }
    
        if (empty($proker->ttd_ketupel)) {
            return redirect()->back()->with('error', 'TTD Ketupel tidak lengkap');
        }
    
        $namaOrganisasi = $proker->organisasi->nama_organisasi;
        $proposalId = $proposal->id;
        
        $user = $this->processStatusFlow($proposalId, $jabatanId, $organisasi, $jabatan, $namaOrganisasi);
    
        if ($user) {
            $result = $this->sendNotificationEmail($user);
    
            if ($result) {
                Session::flash('success', 'Email has been sent.');
            } else {
                Session::flash('error', 'Failed to sent the email.');
                return redirect()->back();
            }
        }


        return redirect()->back()->with('success', 'File Proposal berhasil diupload!');
    }

    public function uploadrab(Request $request, $id)
    {
        $request->validate([
            'file_rab' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $file = $request->file('file_rab');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('rab');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file->move($directory, $filename);

        $rab = Rab::find($id);

        if (!$rab) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Update kolom file_rab
        $rab->file_rab = $filename;
        $rab->save();


        return redirect()->back()->with('success', 'File RAB berhasil diupload!');
    }

    private function processStatusFlow($lpjId, $jabatanId, $organisasi, $jabatan, $namaOrganisasi)
    {
        $mappingCheckLpj = new MappingCheckLpj();
        $signatures = $mappingCheckLpj->updateStatusFlowLpj($lpjId, $jabatanId, $organisasi, $jabatan);
        $status_flow = $signatures['status_flow'] == 0 ? $signatures['status_flow'] + 2 : $signatures['status_flow'] + 1;
        if ($signatures !== false) {
            $signatures = $this->filterTtdList($signatures['ttdList'], $jabatanId, $organisasi);
        }

        $status_code_mapping = [
            0 => 6, // SEKRETARIS
            1 => 6, // REVISI
            2 => stripos($namaOrganisasi, 'UKM') !== false ? 5 : 5, // KETUA UKM atau KETUA HIMA
            3 => 5, // KETUA BEM
            4 => 5, // KETUA BPM
            5 => 4, // PEMBINA
            6 => 8, // KETUA PRODI
            7 => 3, // KETUA JURUSAN
            8 => 2, // KOORDINATOR SUB BAGIAN
            9 => 1  // WAKIL DIREKTUR
        ];

        $codeJabatan = $status_code_mapping[$status_flow] ?? null;

        if ($codeJabatan !== null) {
            $user = User::join('jabatan', 'users.jabatan_id', '=', 'jabatan.jabatan_id')
                ->where('jabatan.code_jabatan', $codeJabatan)
                ->when($status_flow == 2, function($query) use ($namaOrganisasi) {
                    return $query->whereRaw('LOWER(users.organization) = ?', [strtolower($namaOrganisasi)]);
                })
                ->when($status_flow == 3, function($query) {
                    return $query->whereRaw('LOWER(users.organization) LIKE ?', ['%bem%']);
                })
                ->when($status_flow == 4, function($query) {
                    return $query->whereRaw('LOWER(users.organization) LIKE ?', ['%bpm%']);
                })
                ->select('users.email', 'users.name')
                ->first();

            return $user;
        }
        
        return null;
    }

    private function sendNotificationEmail($user)
    {
        if ($user) {
            $emailTarget = $user->email;
            $nameTarget = $user->name;

            $details = [
                'receiver_name' => $nameTarget,
                'proposal_title' => 'Pemberitahuan Proposal Pengajuan Masuk',
                'sender_name' => 'Tim IT',
                'date' => now()->format('Y-m-d')
            ];

            $recipientEmail = $emailTarget;

            return $this->sendEmail($details, $recipientEmail);
        }

        return false;
    }

}
