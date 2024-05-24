<?php

namespace App\Http\Controllers;

use App\Models\Proker;
use Illuminate\Http\Request;

class LpjController extends Controller
{
    public function index()
    {
        $proker = Proker::all();
        // Mengirim data pengguna ke view 'upload-lpj'
        return view('upload-lpj', ['listproker' => $proker]);
    }

    public function indexlpj()
    {
        // Mengirim data pengguna ke view 'lihat-lpj'
        return view('lihat-lpj');
    }

    public function pengecekanlpj()
    {
        // Mengirim data pengguna ke view 'pengecekan-lpj'
        return view('pengecekan-lpj');
    }
    public function pengecekanlpjbpm()
    {
        // Mengirim data pengguna ke view 'pengecekanlpj-bpm'
        return view('pengecekanlpj-bpm');
    }
}
