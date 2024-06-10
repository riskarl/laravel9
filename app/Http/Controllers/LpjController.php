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
            'id_proker' => 'required',
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

        $lpjFolderPath = public_path('lpj');
        if (!File::exists($lpjFolderPath)) {
            File::makeDirectory($lpjFolderPath, 0755, true);
        }

        // Retrieve the LPJ linked with lpjId
        $lpj = LPJ::with('proker.organisasi')->find($lpjId);
        if (!$lpj) {
            Session::flash('error', 'LPJ not found.');
            return redirect()->back();
        }

        $filePath = public_path('lpj/' . $lpj->file_lpj);
        if (!File::exists($filePath)) {
            Session::flash('error', 'LPJ file not found.');
            return redirect()->back();
        }

        $mappingCheckLpj = new MappingCheckLpj();
        $signatures = $mappingCheckLpj->updateStatusFlowLpj($lpjId, $jabatanId, $organisasi, $jabatan);

        if ($signatures !== false) {
            $signatures = $this->filterTtdList($signatures, $jabatanId, $organisasi);
        }

        $proker = Proker::where('id', $lpj->id_proker)->first();
        if (!$proker) {
            return redirect()->back()->with('error', 'Proker not found');
        }

        if (empty($proker->ttd_ketupel)) {
            return redirect()->back()->with('error', 'TTD Ketupel tidak lengkap');
        }

        $ketupel = [
            'name' => $proker->nama_ketupel,
            'nim' => $proker->nim_ketupel,
            'ttd' => public_path('ttd') . '/' . $proker->ttd_ketupel
        ];

        $namaKegiatan = $proker->nama_proker;

        if ($proker->organisasi->nama_organisasi == 'BEM') {
            $html = view('pdf.signatures', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        } elseif (stripos($proker->organisasi->nama_organisasi, 'UKM') !== false) {
            $html = view('pdf.ukm-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        } else {
            $html = view('pdf.hima-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        }

        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');

        $path = public_path('pengesahan');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        // Check if there is already an existing approval file, if so, delete it
        $oldFilePath = public_path('pengesahan/' . $lpj->pengesahan);
        if (File::exists($oldFilePath)) {
            File::delete($oldFilePath);
        }

        $fileName = Str::uuid() . '.pdf';
        $newFilePath = $path . '/' . $fileName;

        $pdf->save($newFilePath);
        $lpj->pengesahan = $fileName;
        $save = $lpj->save();

        if ($signatures != false && $save) {
            Session::flash('success', 'LPJ has been successfully approved.');
        } else {
            Session::flash('error', 'Failed to approve the LPJ.');
        }

        return redirect()->back();
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

        $namaKegiatan = $proker->nama_proker;
        $organisasi = $proker->organisasi->nama_organisasi;

        $mappingCheckLpj = new MappingCheckLpj();
        $signatures = $mappingCheckLpj->signatureCreateLpj($jabatanId, $lpjId, $jabatan);

        $ketupel = [
            'name' => $proker->nama_ketupel,
            'nim' => $proker->nim_ketupel,
            'ttd' => public_path('ttd') . '/' . $proker->ttd_ketupel
        ];

        if ($organisasi == 'BEM') {
            $html = view('pdf.signatures', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        } elseif (stripos($organisasi, 'UKM') !== false) {
            $html = view('pdf.ukm-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        } else {
            $html = view('pdf.hima-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        }

        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');

        $path = public_path('lpj');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        // Check if there is already an existing approval file, if so, delete it
        $oldFilePath = public_path('lpj/' . $lpj->pengesahan);
        if (File::exists($oldFilePath)) {
            File::delete($oldFilePath);
        }

        $fileName = Str::uuid() . '.pdf';
        $newFilePath = $path . '/' . $fileName;

        $pdf->save($newFilePath);
        $lpj->pengesahan = $fileName;
        $lpj->save();

        return $pdf->stream('document.pdf');
    }


}
