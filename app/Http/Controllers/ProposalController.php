<?php

namespace App\Http\Controllers;

use App\Models\Proker;
use Illuminate\Http\Request;
use App\Models\Proposal;
use DB;
use Session;
use App\Models\MappingCheck;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;


class ProposalController extends Controller
{
    public function index()
    {
        //mendapatkan informasi pengguna saat ini
        $currentUser = $this->getCurrentUser();
        //mengambil data proker dengan organisasi dan proposal terkait
        $proker = Proker::with(['organisasi', 'proposal'])->get();
        //mendapatkan data jabatan dari pengguna saat ini
        $jabatan = $currentUser['jabatan'];
        //mendapatkan organisasi pengguna saat ini
        $organisasiUser = $currentUser['organisasi'];

        // Mengirim data pengguna ke view 'upload-proposal'
        return view('upload-proposal', ['listproker' => $proker, 'jabatan' => $jabatan, 'orguser' => $organisasiUser]);
    }


    public function indexproposal()
    {
        // Mengirim data pengguna ke view 'lihat-proposal'
        return view('lihat-proposal');
    }

    public function pengecekanproposal()
    {
        $currentUser = $this->getCurrentUser();
        $proker = Proker::with(['organisasi', 'proposal'])->get();
        $organisasiUser = $currentUser['organisasi'];
        $codeJabatan = $currentUser['code_jabatan'];
        // Mengirim data pengguna ke view 'pengecekan-proposal'
        return view('pengecekan-proposal', ['listproker' => $proker, 'orguser' => $organisasiUser, 'codeJabatan' => $codeJabatan]);
    }

    public function pengecekanproposalbpm()
    {
        // Mengirim data pengguna ke view 'pengecekanproposal-bpm'
        return view('pengecekanproposal-bpm');
    }

    public function approvedProposal($proposalId)
    {
        $currentUser = $this->getCurrentUser();
        $jabatanId = $currentUser['code_jabatan'];
        $jabatan = $currentUser['jabatan'];
        $organisasi = $currentUser['organisasi'];
    
        $proposal = Proposal::find($proposalId);
        if (!$proposal) {
            Session::flash('error', 'Proposal not found.');
            return redirect()->back();
        }
    
        $filePath = public_path('files/' . $proposal->file_proposal);
        if (!File::exists($filePath)) {
            Session::flash('error', 'Proposal file not found.');
            return redirect()->back();
        }
    
        $mappingCheck = new MappingCheck();
        $signatures = $mappingCheck->updateStatusFlow($proposalId, $jabatanId, $organisasi, $jabatan);
    
        if ($signatures !== false) {
            $signatures = $this->filterTtdList($signatures, $jabatanId, $organisasi);
        }
    
        $proker = Proker::where('id', $proposal->id_proker)->first();
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
    
        $html = view('pdf.signatures', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');
    
        $path = public_path('pengesahan');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    
        // Cek apakah sudah ada file pengesahan sebelumnya, jika ada maka hapus
        $oldFilePath = public_path('pengesahan/' . $proposal->pengesahan);
        if (File::exists($oldFilePath)) {
            File::delete($oldFilePath);
        }
    
        $fileName = Str::uuid() . '.pdf';
        $newFilePath = $path . '/' . $fileName;
    
        $pdf->save($newFilePath);
        $proposal->pengesahan = $fileName;
        $save = $proposal->save();
    
        if ($signatures != false && $save) {
            Session::flash('success', 'Proposal has been successfully approved.');
        } else {
            Session::flash('error', 'Failed to approve the proposal.');
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

    public function updateRevisi(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $jabatanId = $currentUser['code_jabatan'];
        $jabatan = $currentUser['jabatan'];
        $organisasi = $currentUser['organisasi'];
        $proposalId = $request->input('proposal_id');
        $catatan = $request->input('catatan');

        // Create a new instance of MappingCheck
        $mappingCheck = new MappingCheck();

        // Attempt to update the status flow
        if ($mappingCheck->updateRevisi($proposalId, $jabatanId, $organisasi, $jabatan, $catatan)) {
            Session::flash('success', 'Proposal has been successfully approved.');
        } else {
            Session::flash('error', 'Failed to approve the proposal.');
        }

        return redirect()->back();
    }

    public function createSignaturePdf(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $proposalId = $request->input('proposal_id');
        $jabatanId = $currentUser['jabatan_id'];
        $jabatan = $currentUser['jabatan'];
        $namaKegiatan = $request->input('proker');
        $organisasi = $request->input('organisasi');

        $proposal = Proposal::find($proposalId);
        if (!$proposal) {
            return redirect()->back()->with('error', 'Proposal not found');
        }

        $proker = Proker::where('id', $proposal->id_proker)->first();
        if (!$proker) {
            return redirect()->back()->with('error', 'Proker not found');
        }

        if (empty($proker->ttd_ketupel)) {
            return redirect()->back()->with('error', 'TTD Ketupel tidak lengkap');
        }

        $model = new MappingCheck();
        $signatures = $model->signatureCreate($jabatanId, $proposalId, $jabatan);

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

        $path = public_path('pengesahan');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        // Cek apakah sudah ada file pengesahan sebelumnya, jika ada maka hapus
        $oldFilePath = public_path('pengesahan/' . $proposal->pengesahan);
        if (File::exists($oldFilePath)) {
            File::delete($oldFilePath);
        }

        // Membuat nama file baru dengan UUID untuk memastikan unik
        $fileName = Str::uuid() . '.pdf';
        $filePath = $path . '/' . $fileName;

        $pdf->save($filePath);
        $proposal->pengesahan = $fileName;
        $proposal->save();

        return $pdf->stream('document.pdf');
    }


}
