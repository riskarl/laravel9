<?php

namespace App\Http\Controllers;

use App\Models\Proker;
use Illuminate\Http\Request;
use App\Models\Proposal;

class ProposalController extends Controller
{
    public function index()
    {
        $proker = Proker::all();

        // Mengirim data pengguna ke view 'upload-proposal'
        return view('upload-proposal', ['listproker' => $proker]);
    }


    public function indexproposal()
    {
        // Mengirim data pengguna ke view 'lihat-proposal'
        return view('lihat-proposal');
    }

    public function pengecekanproposal()
    {
        // Mengirim data pengguna ke view 'pengecekan-proposal'
        return view('pengecekan-proposal');
    }

    public function pengecekanproposalbpm()
    {
        // Mengirim data pengguna ke view 'pengecekanproposal-bpm'
        return view('pengecekanproposal-bpm');
    }

}
