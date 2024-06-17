<?php

namespace App\Http\Controllers;

use App\Models\Rab;
use Illuminate\Http\Request;
use App\Models\LPJ;
use App\Models\Proposal;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Proker;
use App\Models\MappingCheck;

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

        $codeJabatan = 6;
        $status_flow = 0;
        $namaOrganisasi = $proker->organisasi->nama_organisasi;

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

                $sendEmail = $this->sendNotificationEmail($user);

                if($sendEmail){
                    return redirect()->back()->with('success', 'File Proposal berhasil diupload!');
                }else{
                    return redirect()->back()->with('error', 'gagal kirim email!');   
                }
        }
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

    private function filterTtdList($ttdList, $jabatanId, $organisasi)
    {
        foreach ($ttdList as &$ttd) {
            $isMatch = false;
    
            if ($jabatanId == 5) {
                if (stripos($organisasi, 'HIMA') !== false) {
                    $isMatch = stripos($ttd['organisasi'], 'HIMA') !== false && $ttd['code_jabatan'] == 5;
                } elseif (stripos($organisasi, 'UKM') !== false) {
                    $isMatch = stripos($ttd['organisasi'], 'UKM') !== false && $ttd['code_jabatan'] == 5;
                } elseif ($organisasi == 'BEM') {
                    $isMatch = ($ttd['organisasi'] == 'BEM' || stripos($ttd['organisasi'], 'HIMA') !== false || stripos($ttd['organisasi'], 'UKM') !== false) && $ttd['code_jabatan'] == 5;
                }elseif ($organisasi == 'BPM') {
                    $isMatch = ($ttd['organisasi'] == 'BPM' || $ttd['organisasi'] == 'BEM' || stripos($ttd['organisasi'], 'HIMA') !== false || stripos($ttd['organisasi'], 'UKM') !== false) && $ttd['code_jabatan'] == 5;
                }
            } else if ($jabatanId == 4) {
                $isMatch = $ttd['code_jabatan'] == 4 || $ttd['code_jabatan'] == 5;
            } else if ($jabatanId == 8) {
                $isMatch = $ttd['code_jabatan'] == 8 || $ttd['code_jabatan'] == 4 || $ttd['code_jabatan'] == 5;
            } else if ($jabatanId == 3) {
                $isMatch = $ttd['code_jabatan'] == 3 || $ttd['code_jabatan'] == 8 || $ttd['code_jabatan'] == 4 || $ttd['code_jabatan'] == 5;
            } else if ($jabatanId == 2) {
                $isMatch = ($ttd['code_jabatan'] == 2 || $ttd['code_jabatan'] == 3 || $ttd['code_jabatan'] == 8 || $ttd['code_jabatan'] == 4 || $ttd['code_jabatan'] == 5) && $ttd['role'] != 1;
            } else if ($jabatanId == 1) {
                $isMatch = $ttd['code_jabatan'] == 2 || $ttd['code_jabatan'] == 2 || $ttd['code_jabatan'] == 3 || $ttd['code_jabatan'] == 8 || $ttd['code_jabatan'] == 4 || $ttd['code_jabatan'] == 5;
            }
            // Jika tidak cocok, setel semua atribut ke null
            if (!$isMatch) {
                $ttd = array_fill_keys(array_keys($ttd), null);
            }
        }
    
        return $ttdList;
    }

}
