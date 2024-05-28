<?php

namespace App\Http\Controllers;

use App\Models\Proker;
use Illuminate\Http\Request;
use App\Models\Proposal;
use DB;
use Session;

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

        // var_dump($proker);die;

        // Mengirim data pengguna ke view 'pengecekan-proposal'
        return view('pengecekan-proposal', ['listproker' => $proker, 'orguser' => $organisasiUser]);
    }

    public function pengecekanproposalbpm()
    {
        // Mengirim data pengguna ke view 'pengecekanproposal-bpm'
        return view('pengecekanproposal-bpm');
    }

    public function downloadSignaturePdf()
    {
        $signatures = [
            'path/to/signature1.jpg',
            'path/to/signature2.jpg',
            'path/to/signature3.jpg',
            'path/to/signature4.jpg',
            'path/to/signature5.jpg',
            // Tambahkan path tanda tangan lainnya jika ada
        ];

        return $this->generatePdfWithSignatures($signatures);
    }

}
