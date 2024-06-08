<?php

namespace App\Http\Controllers;

use App\Models\Proker;
use App\Models\LPJ;
use Illuminate\Http\Request;
use DB;
use Session;
use App\Models\MappingCheckLpj;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

class LpjController extends Controller
{
    public function index()
    {
        $currentUser = $this->getCurrentUser();
        $lpj = LPJ::all();
        $organisasiUser = $currentUser['organisasi'];
        $proker = Proker::with(['lpj', 'proposal'])
            ->whereHas('proposal', function ($query) {
                $query->where('status_flow', 9);
            })
            ->get();

        // Iterasi melalui setiap proker untuk memodifikasi nilai pengesahan
        foreach ($proker as $item) {
            if ($item->lpj && $item->lpj->status_flow_lpj != 9) {
                $item->proposal->pengesahan = 'File tidak ada';
            }
        }

        return view('upload-lpj', ['listproker' => $proker, 'orguser' => $organisasiUser, 'lpj' => $lpj]);
    }


    public function indexlpj()
    {
        return view('lihat-lpj');
    }

    public function pengecekanlpj()
    {
        $currentUser = $this->getCurrentUser();
        $proker = Proker::with(['organisasi', 'lpj'])->get();
        $organisasiUser = $currentUser['organisasi'];
        $codeJabatan = $currentUser['code_jabatan'];

        return view('pengecekan-lpj', ['listproker' => $proker, 'orguser' => $organisasiUser, 'codeJabatan' => $codeJabatan]);
    }

    public function pengecekanlpjbpm()
    {
        return view('pengecekanlpj-bpm');
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'file_lpj' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'dana_disetujui' => 'required|numeric',
            'id_proker' => 'required|exists:prokers,id',
        ]);
    
        $file = $request->file('file_lpj');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('lpj');
    
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    
        // Cek apakah data LPJ untuk id_proker ini sudah ada
        $lpj = LPJ::where('id_proker', $request->id_proker)->first();
    
        if ($lpj) {
            // Jika ada, update file dan data lainnya
            if (File::exists($directory . '/' . $lpj->file_lpj)) {
                File::delete($directory . '/' . $lpj->file_lpj);
            }
            $lpj->file_lpj = $filename;
            $lpj->dana_disetujui = $validatedData['dana_disetujui'];
            $lpj->status = 'Pending';
            $lpj->catatan = 'Belum ada catatan';
        } else {
            // Jika tidak ada, buat data baru
            $lpj = new LPJ();
            $lpj->file_lpj = $filename;
            $lpj->dana_disetujui = $validatedData['dana_disetujui'];
            $lpj->status = 'Pending';
            $lpj->catatan = 'Belum ada catatan';
            $lpj->id_proker = $request->id_proker;
        }
    
        // Pindahkan file ke direktori yang ditentukan
        $file->move($directory, $filename);
    
        // Simpan perubahan atau data baru
        $lpj->save();
    
        return redirect('/uploadlpj')->with('success', 'File LPJ berhasil diupload!');
    }    

    public function update(Request $request, $id_proker)
    {
        // Validasi input
        $validatedData = $request->validate([
            'file_lpj' => 'nullable|file|mimes:pdf,doc,docx',
            'dana_disetujui' => 'required|numeric',
        ]);

        // Temukan LPJ berdasarkan id_proker
        $lpj = LPJ::where('id_proker', $id_proker)->first();

        if (!$lpj) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Jika ada file yang diunggah
        if ($request->hasFile('file_lpj')) {
            $file = $request->file('file_lpj');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $directory = public_path('lpj');

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Pindahkan file baru ke direktori
            $file->move($directory, $filename);

            // Hapus file lama jika ada
            if ($lpj->file_lpj && File::exists($directory . '/' . $lpj->file_lpj)) {
                File::delete($directory . '/' . $lpj->file_lpj);
            }

            // Perbarui kolom file_lpj dengan nama file baru
            $lpj->file_lpj = $filename;
        }

        // Perbarui kolom dana_disetujui
        $lpj->dana_disetujui = $validatedData['dana_disetujui'];

        // Simpan perubahan
        $lpj->save();

        return redirect('/uploadlpj')->with('success', 'File LPJ berhasil diperbarui!');
    }


    public function approvedLpj($lpjId)
    {
        $currentUser = $this->getCurrentUser();
        $jabatanId = $currentUser['code_jabatan'];
        $jabatan = $currentUser['jabatan'];
        $organisasi = $currentUser['organisasi'];

        // Mendapatkan LPJ yang terkait dengan lpjId
        $lpj = LPJ::find($lpjId);

        // Jika tidak ditemukan LPJ, return false
        if (!$lpj) {
            Session::flash('error', 'LPJ not found.');
            return redirect()->back();
        }

        // Mendapatkan path dari file LPJ
        $filePath = public_path('lpj/' . $lpj->file_lpj);

        // Memeriksa apakah file LPJ ada
        if (!File::exists($filePath)) {
            Session::flash('error', 'LPJ file not found.');
            return redirect()->back();
        }

        $mappingCheckLpj = new MappingCheckLpj();

        if ($mappingCheckLpj->updateStatusFlowLpj($lpjId, $jabatanId, $organisasi, $jabatan)) {
            Session::flash('success', 'LPJ has been successfully approved.');
        } else {
            Session::flash('error', 'Failed to approve the LPJ.');
        }

        return redirect()->back();
    }


    public function updateRevisiLpj(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $jabatanId = $currentUser['code_jabatan'];
        $jabatan = $currentUser['jabatan'];
        $organisasi = $currentUser['organisasi'];
        $lpjId = $request->input('lpj_id');
        $catatan = $request->input('catatan');
        $danadisetujui = $request->input('dana_disetujui');

        $mappingCheckLpj = new MappingCheckLpj();

        if ($mappingCheckLpj->updateRevisiLpj($lpjId, $jabatanId, $organisasi, $jabatan, $catatan)) {
            Session::flash('success', 'LPJ has been successfully revised.');
        } else {
            Session::flash('error', 'Failed to revise the LPJ.');
        }

        return redirect()->back();
    }

    public function createSignaturePdf(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $lpjId = $request->input('lpj_id');
        $jabatanId = $currentUser['jabatan_id'];
        $jabatan = $currentUser['jabatan'];
        $namaKegiatan = $request->input('proker');
        $organisasi = $request->input('organisasi');

        $lpj = LPJ::find($lpjId);
        if (!$lpj) {
            return redirect()->back()->with('error', 'LPJ not found');
        }

        $proker = Proker::where('id', $lpj->id_proker)->first();
        if (!$proker) {
            return redirect()->back()->with('error', 'Proker not found');
        }

        if (empty($proker->ttd_ketupel)) {
            return redirect()->back()->with('error', 'TTD Ketupel tidak lengkap');
        }

        $mappingCheckLpj = new MappingCheckLpj();
        $result = $mappingCheckLpj->signatureCreateLpj($jabatanId, $lpjId, $jabatan);

        if ($result) {
            Session::flash('success', 'LPJ has been successfully Approve.');
        } else {
            Session::flash('error', 'Failed to Approve the LPJ.');
        }

        return redirect()->back();
    }

}
