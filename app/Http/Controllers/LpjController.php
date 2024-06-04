<?php

namespace App\Http\Controllers;

use App\Models\Proker;
use App\Models\LPJ;
use Illuminate\Http\Request;
use DB;
use Session;
use App\Models\MappingCheckLpj;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

class LpjController extends Controller
{
    public function index()
    {
        $currentUser = $this->getCurrentUser();
        $organisasiUser = $currentUser['organisasi'];
        $proker = Proker::with(['lpj', 'proposal'])
            ->whereHas('proposal', function($query) {
                $query->where('status_flow', 9);
            })
            ->get();

        // Iterasi melalui setiap proker untuk memodifikasi nilai pengesahan
        foreach ($proker as $item) {
            if ($item->lpj && $item->lpj->status_flow_lpj != 9) {
                $item->proposal->pengesahan = 'File tidak ada';
            }
        }

        return view('upload-lpj', ['listproker' => $proker,'orguser' => $organisasiUser]);
    }


    public function indexlpj()
    {
        return view('lihat-lpj');
    }

    public function pengecekanlpj()
    {
        $currentUser = $this->getCurrentUser();
        $proker = Proker::with(['organisasi', 'lpj'])->get();
        $organisasiUser = $currentUser['organisasi'];
        $codeJabatan = $currentUser['code_jabatan'];

        return view('pengecekan-lpj', ['listproker' => $proker, 'orguser' => $organisasiUser, 'codeJabatan' => $codeJabatan]);
    }

    public function pengecekanlpjbpm()
    {
        return view('pengecekanlpj-bpm');
    }

    public function approvedLpj($lpjId)
    {
        $currentUser = $this->getCurrentUser();
        $jabatanId = $currentUser['code_jabatan'];
        $jabatan = $currentUser['jabatan'];
        $organisasi = $currentUser['organisasi'];

        // Mendapatkan LPJ yang terkait dengan lpjId
        $lpj = LPJ::find($lpjId);

        // Jika tidak ditemukan LPJ, return false
        if (!$lpj) {
            Session::flash('error', 'LPJ not found.');
            return redirect()->back();
        }

        // Mendapatkan path dari file LPJ
        $filePath = public_path('lpj/' . $lpj->file_lpj);

        // Memeriksa apakah file LPJ ada
        if (!File::exists($filePath)) {
            Session::flash('error', 'LPJ file not found.');
            return redirect()->back();
        }

        $mappingCheckLpj = new MappingCheckLpj();

        if ($mappingCheckLpj->updateStatusFlowLpj($lpjId, $jabatanId, $organisasi, $jabatan)) {
            Session::flash('success', 'LPJ has been successfully approved.');
        } else {
            Session::flash('error', 'Failed to approve the LPJ.');
        }

        return redirect()->back();
    }


    public function updateRevisiLpj(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $jabatanId = $currentUser['code_jabatan'];
        $jabatan = $currentUser['jabatan'];
        $organisasi = $currentUser['organisasi'];
        $lpjId = $request->input('lpj_id');
        $catatan = $request->input('catatan');

        $mappingCheckLpj = new MappingCheckLpj();

        if ($mappingCheckLpj->updateRevisiLpj($lpjId, $jabatanId, $organisasi, $jabatan, $catatan)) {
            Session::flash('success', 'LPJ has been successfully revised.');
        } else {
            Session::flash('error', 'Failed to revise the LPJ.');
        }

        return redirect()->back();
    }

    public function createSignaturePdf(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $lpjId = $request->input('lpj_id');
        $jabatanId = $currentUser['jabatan_id'];
        $jabatan = $currentUser['jabatan'];
        $namaKegiatan = $request->input('proker');
        $organisasi = $request->input('organisasi');

        $lpj = LPJ::find($lpjId);
        if (!$lpj) {
            return redirect()->back()->with('error', 'LPJ not found');
        }

        $proker = Proker::where('id', $lpj->id_proker)->first();
        if (!$proker) {
            return redirect()->back()->with('error', 'Proker not found');
        }

        if (empty($proker->ttd_ketupel)) {
            return redirect()->back()->with('error', 'TTD Ketupel tidak lengkap');
        }

        $mappingCheckLpj = new MappingCheckLpj();
        $result = $mappingCheckLpj->signatureCreateLpj($jabatanId, $lpjId, $jabatan);

        if($result)
        {
            Session::flash('success', 'LPJ has been successfully Approve.');
        } else {
            Session::flash('error', 'Failed to Approve the LPJ.');
        }

        return redirect()->back();
    }

}
