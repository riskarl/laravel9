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
        // Mengirim data pengguna ke view 'upload-proposal'
        return view('upload-proposal', ['listproker' => $proker, 'jabatan' => $jabatan]);
    }


    public function indexproposal()
    {
        // Mengirim data pengguna ke view 'lihat-proposal'
        return view('lihat-proposal');
    }

    public function pengecekanproposal()
    {
        // Mengambil data dari database dengan JOIN
        // $listproker = DB::table('tb_proker')
        //     ->join('tb_proposal', 'tb_proker.id', '=', 'tb_proposal.id_proker')
        //     ->select('tb_proker.id', 'tb_proker.nama_organisasi', 'tb_proker.nama_proker', 'tb_proposal.file_proposal', 'tb_proposal.status', 'tb_proposal.catatan')
        //     ->get();

        // Mengirim data ke view 'pengecekan-proposal'
        // return view('pengecekan-proposal', ['listproker' => $listproker]);

        $proker = Proker::with(['organisasi', 'proposal'])->get();

        // Mengirim data pengguna ke view 'pengecekan-proposal'
        return view('pengecekan-proposal', ['listproker' => $proker]);
    }

    public function pengecekanproposalbpm()
    {
        // Mengirim data pengguna ke view 'pengecekanproposal-bpm'
        return view('pengecekanproposal-bpm');
    }

}
