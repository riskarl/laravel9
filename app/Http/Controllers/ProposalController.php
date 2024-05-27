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
        $proker = Proker::with(['organisasi', 'proposal'])->get();
        $jabatan = Session::get('jabatan')['jabatan'];
        $organisasiUser = Session::get('user')['organization'];
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
        $proker = Proker::with(['organisasi', 'proposal'])->get();
        $organisasiUser = Session::get('user')['organization'];

        // var_dump($proker);die;

        // Mengirim data pengguna ke view 'pengecekan-proposal'
        return view('pengecekan-proposal', ['listproker' => $proker, 'orguser' => $organisasiUser]);
    }

    public function pengecekanproposalbpm()
    {
        // Mengirim data pengguna ke view 'pengecekanproposal-bpm'
        return view('pengecekanproposal-bpm');
    }

}
