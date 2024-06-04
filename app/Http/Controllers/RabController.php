<?php

namespace App\Http\Controllers;

use App\Models\Proker;
use Illuminate\Http\Request;

class RabController extends Controller
{
    public function index()
    {
        $currentUser = $this->getCurrentUser();
        $proker = Proker::with('rab')->get();
        $organisasiUser = $currentUser['organisasi'];
        // Mengirim data pengguna ke view 'upload-rab'
        return view('upload-rab', ['listproker' => $proker, 'orguser' => $organisasiUser]);
    }

    public function unduhsrpd()
    {
        // Mengirim data pengguna ke view 'unduh-srpd'
        return view('unduh-srpd');
    }

    public function uploadsrpd()
    {
        $proker = Proker::with(['organisasi', 'rab'])->get();
        // Mengirim data pengguna ke view 'pengecekan-rab'
        return view('pengecekan-rab',['listproker' => $proker]);
    }
}
