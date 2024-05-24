<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RabController extends Controller
{
    public function index()
    {
        // Mengirim data pengguna ke view 'upload-rab'
        return view('upload-rab');
    }

    public function unduhsrpd()
    {
        // Mengirim data pengguna ke view 'unduh-srpd'
        return view('unduh-srpd');
    }

    public function uploadsrpd()
    {
        // Mengirim data pengguna ke view 'pengecekan-rab'
        return view('pengecekan-rab');
    }
}
