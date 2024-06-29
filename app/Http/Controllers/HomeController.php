<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\Format;
use App\Models\LPJ;
use App\Models\Organisasi;
use App\Models\Proker;
use App\Models\Proposal;
use App\Models\Rab;
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
        $approvedProposals = Proposal::where('status_flow', 9)->count();
        $processedProposals = $totalproposal - $approvedProposals;
        $approvedLpj = LPJ::where('status_flow_lpj', 9)->count();
        $processedLpj = $totallpj - $approvedLpj;
        $prokerTanpaProposal = Proker::leftJoin('tb_proposal', 'tb_proker.id', '=', 'tb_proposal.id_proker')
            ->whereNull('tb_proposal.id')
            ->count();

        return view('dashboard', ['prokerTanpaProposal' => $prokerTanpaProposal, 'processedLpj' => $processedLpj, 'approvedLpj' => $approvedLpj, 'approvedProposals' => $approvedProposals, 'processedProposals' => $processedProposals, 'totalakun' => $totalakun, 'totalorganisasi' => $totalorganisasi, 'totalproposal' => $totalproposal, 'totallpj' => $totallpj, 'totalproker' => $totalproker]);
    }

    public function indexorganisasi()
    {
        $currentUser = $this->getCurrentUser();
        $jabatan = $currentUser['jabatan'];
        $organisasiUser = $currentUser['organisasi'];

        // Filter berdasarkan nama_organisasi dari organisasi user yang sedang login
        $totalproposal = Proposal::whereHas('proker.organisasi', function ($query) use ($organisasiUser) {
            $query->where('nama_organisasi', $organisasiUser);
        })->count();

        // Menghitung jumlah LPJ berdasarkan nama_organisasi di Organisasi
        $totallpj = LPJ::whereHas('proker.organisasi', function ($query) use ($organisasiUser) {
            $query->where('nama_organisasi', $organisasiUser);
        })->count();

        $totalproker = Proker::whereHas('organisasi', function ($query) use ($organisasiUser) {
            $query->where('nama_organisasi', $organisasiUser);
        })->count();

        return view('dashboard-organisasi', [
            'jabatan' => $jabatan,
            'totalproker' => $totalproker,
            'organisasiUser' => $organisasiUser,
            'totalproposal' => $totalproposal,
            'totallpj' => $totallpj
        ]);
    }
    public function indexbpm()
    {
        $totalAllocation = 85000000;
        $bemAllocation = 19992000;
        $ukmAllocation = 8316000;
        $himaAllocation = 18711000;
        $bemPercentage = ($bemAllocation / $totalAllocation) * 100;
        $ukmPercentage = ($ukmAllocation / $totalAllocation) * 100;
        $himaPercentage = ($himaAllocation / $totalAllocation) * 100;
        $data = [
            'labels' => ['BEM', 'UKM', 'HIMA'],
            'datasets' => [
                [
                    'data' => [$bemPercentage, $ukmPercentage, $himaPercentage],
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56'],
                    'hoverBackgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56'],
                ],
            ],
        ];
        $totalformat = Format::count();
        $totalanggaran = Anggaran::count();
        $totalrab = Rab::count();
        $totalnosrpd = Rab::whereNull('file_srpd')->count();
        $totalsrpd = Rab::whereNotNull('file_srpd')->count();
        return view('dashboard-bpm', [
            'totalformat' => $totalformat,
            'totalanggaran' => $totalanggaran,
            'totalrab' => $totalrab,
            'totalnosrpd' => $totalnosrpd,
            'totalsrpd' => $totalsrpd,
            'data' => $data
        ]);
    }
}
