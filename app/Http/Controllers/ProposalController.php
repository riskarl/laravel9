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
        //mendapatkan informasi pengguna saat ini
        $currentUser = $this->getCurrentUser();
        //mendapatkan code_jabatan dari pengguna saat ini
        $jabatanId = $currentUser['code_jabatan'];
        //mendapatkan jabatan dari pengguna saat ini
        $jabatan = $currentUser['jabatan'];
        //mendapatkan organisasi dari pengguna saat ini
        $organisasi = $currentUser['organisasi'];

        // Create a new instance of MappingCheck
        $mappingCheck = new MappingCheck();

        // Attempt to update the status flow
        if ($mappingCheck->updateStatusFlow($proposalId, $jabatanId, $organisasi, $jabatan)) {
            Session::flash('success', 'Proposal has been successfully approved.');
        } else {
            Session::flash('error', 'Failed to approve the proposal.');
        }

        return redirect()->back();
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
        $namaKegiatan = $request->input('proker'); // Pastikan parameter inputnya sesuai
        $organisasi = $request->input('organisasi');
    
        $proposal = Proposal::find($proposalId);
        if (!$proposal) {
            return redirect()->back()->with('error', 'Proposal not found');
        }
    
        // Ambil data Proker terkait dari Proposal
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
        } elseif (strpos($organisasi, 'UKM') !== false) {
            $html = view('pdf.ukm-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        } else {
            $html = view('pdf.hima-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        }
    
        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');
    
        // Membuat direktori jika belum ada
        $path = public_path('pengesahan');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    
        // Membuat nama file dengan UUID
        $fileName = Str::uuid() . '.pdf';
        $filePath = $path . '/' . $fileName;
    
        // Menyimpan PDF ke direktori public/pengesahan
        $pdf->save($filePath);
    
        // Menyimpan nama file di database
        $proposal->pengesahan = $fileName;
        $proposal->save();
    
        // Mengirim PDF ke browser untuk ditampilkan maupun diunduh
        return $pdf->stream('document.pdf');
    }
    
}
