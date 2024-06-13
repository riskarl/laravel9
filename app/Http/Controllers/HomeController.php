<?php

namespace App\Http\Controllers;

use App\Models\LPJ;
use App\Models\Organisasi;
use App\Models\Proker;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $totalakun = User::count();
        $totalorganisasi = Organisasi::count();
        $totalproposal = Proposal::count();
        $totalproker = Proker::count();
        $totallpj = LPJ::count();
        return view('dashboard', ['totalakun' => $totalakun, 'totalorganisasi' => $totalorganisasi, 'totalproposal' => $totalproposal, 'totallpj' => $totallpj, 'totalproker' => $totalproker]);
    }
}
