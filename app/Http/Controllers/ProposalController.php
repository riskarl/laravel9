<?php

namespace App\Http\Controllers;

use App\Models\Proker;
use Illuminate\Http\Request;
use App\Models\Proposal;
use DB;
use Session;
use App\Models\MappingCheck;

class ProposalController extends Controller
{
    public function index()
    {
        $currentUser = $this->getCurrentUser();

        $proker = Proker::with(['organisasi', 'proposal'])->get();
        $jabatan = $currentUser['jabatan'];
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

        // var_dump($proker);die;

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
        $namaKegiatan = $request->input('proker'); // Pastikan parameter inputnya sesuai
        $organisasi = $request->input('organisasi');

        $model = new MappingCheck();
        $signatures = $model->signatureCreate($jabatanId, $proposalId);

        $proposal = Proposal::find($proposalId);
        if (!$proposal) {
            return redirect()->back()->with('error', 'Proposal not found');
        }

        // Ambil data Proker terkait dari Proposal
        $proker = Proker::where('id', $proposal->id_proker)->first();
        if (!$proker) {
            return redirect()->back()->with('error', 'Proker not found');
        }

        $ketupel = [
            'name' => $proker->nama_ketupel,
            'ttd' => public_path('ttd') . '/' . $proker->ttd_ketupel
        ];

        // Memasukkan nama kegiatan ke dalam array data yang dikirim ke fungsi pembuat PDF
        return $this->generatePdfWithSignatures($signatures, $namaKegiatan, $organisasi, $ketupel);
    }
}
