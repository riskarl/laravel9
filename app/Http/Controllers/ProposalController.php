<?php

namespace App\Http\Controllers;

use App\Models\Proker;
use Illuminate\Http\Request;
use App\Models\Proposal;
use DB;
use Session;
use App\Models\MappingCheck;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;


class ProposalController extends Controller
{
    public function index()
    {
        //mendapatkan informasi pengguna saat ini
        $currentUser = $this->getCurrentUser();
        //mengambil data proker dengan organisasi dan proposal terkait
        $proker = Proker::with(['organisasi', 'proposal'])->get();
        //mendapatkan data jabatan dari pengguna saat ini
        $jabatan = $currentUser['jabatan'];
        //mendapatkan organisasi pengguna saat ini
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
        $codeJabatan = $currentUser['code_jabatan'];
        // Mengirim data pengguna ke view 'pengecekan-proposal'
        return view('pengecekan-proposal', ['listproker' => $proker, 'orguser' => $organisasiUser, 'codeJabatan' => $codeJabatan]);
    }

    public function pengecekanproposalbpm()
    {
        // Mengirim data pengguna ke view 'pengecekanproposal-bpm'
        return view('pengecekanproposal-bpm');
    }

    public function approvedProposal($proposalId)
    {
        // Mendapatkan informasi pengguna saat ini
        $currentUser = $this->getCurrentUser();
        // Mendapatkan code_jabatan dari pengguna saat ini
        $jabatanId = $currentUser['code_jabatan'];
        // Mendapatkan jabatan dari pengguna saat ini
        $jabatan = $currentUser['jabatan'];
        // Mendapatkan organisasi dari pengguna saat ini
        $organisasi = $currentUser['organisasi'];

        // Mendapatkan proposal yang terkait dengan proposalId
        $proposal = Proposal::find($proposalId);

        // Jika tidak ditemukan proposal, return false
        if (!$proposal) {
            Session::flash('error', 'Proposal not found.');
            return redirect()->back();
        }

        // Mendapatkan path dari file proposal
        $filePath = public_path('files/' . $proposal->file_proposal);

        // Memeriksa apakah file proposal ada
        if (!File::exists($filePath)) {
            Session::flash('error', 'Proposal file not found.');
            return redirect()->back();
        }

        // Create a new instance of MappingCheck
        $mappingCheck = new MappingCheck();

        $dataTtd = $mappingCheck->updateStatusFlow($proposalId, $jabatanId, $organisasi, $jabatan);

        if ($dataTtd !== false) {
            if ($jabatanId == 5) {
                if (stripos($organisasi, 'HIMA') !== false) {
                    $dataTtd = $this->nullifyNonMatchingTtd($dataTtd, ['HIMA']);
                } elseif (stripos($organisasi, 'UKM') !== false) {
                    $dataTtd = $this->nullifyNonMatchingTtd($dataTtd, ['UKM']);
                } elseif ($organisasi == 'BEM') {
                    $dataTtd = $this->nullifyNonMatchingTtd($dataTtd, ['BEM', 'HIMA', 'UKM']);
                }
            }
        }
    
        var_dump($dataTtd); die;

        // Attempt to update the status flow
        if ($dataTtd != false) {
            Session::flash('success', 'Proposal has been successfully approved.');
        } else {
            Session::flash('error', 'Failed to approve the proposal.');
        }

        return redirect()->back();
    }

    private function nullifyNonMatchingTtd($ttdList, $organisasiList)
    {
        foreach ($ttdList as &$ttd) {
            $match = false;

            foreach ($organisasiList as $org) {
                if (stripos($ttd['organisasi'], $org) !== false) {
                    $match = true;
                    break;
                }
            }

            if (!$match) {
                $ttd = array_fill_keys(array_keys($ttd), null);
            }
        }

        return $ttdList;
    }


    public function updateRevisi(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $jabatanId = $currentUser['code_jabatan'];
        $jabatan = $currentUser['jabatan'];
        $organisasi = $currentUser['organisasi'];
        $proposalId = $request->input('proposal_id');
        $catatan = $request->input('catatan');

        // Create a new instance of MappingCheck
        $mappingCheck = new MappingCheck();

        // Attempt to update the status flow
        if ($mappingCheck->updateRevisi($proposalId, $jabatanId, $organisasi, $jabatan, $catatan)) {
            Session::flash('success', 'Proposal has been successfully approved.');
        } else {
            Session::flash('error', 'Failed to approve the proposal.');
        }

        return redirect()->back();
    }

    public function createSignaturePdf(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $proposalId = $request->input('proposal_id');
        $jabatanId = $currentUser['jabatan_id'];
        $jabatan = $currentUser['jabatan'];
        $namaKegiatan = $request->input('proker'); // Pastikan parameter inputnya sesuai
        $organisasi = $request->input('organisasi');

        $proposal = Proposal::find($proposalId);
        if (!$proposal) {
            return redirect()->back()->with('error', 'Proposal not found');
        }

        // Ambil data Proker terkait dari Proposal
        $proker = Proker::where('id', $proposal->id_proker)->first();
        if (!$proker) {
            return redirect()->back()->with('error', 'Proker not found');
        }

        if (empty($proker->ttd_ketupel)) {
            return redirect()->back()->with('error', 'TTD Ketupel tidak lengkap');
        }

        $model = new MappingCheck();
        $signatures = $model->signatureCreate($jabatanId, $proposalId, $jabatan);

        $ketupel = [
            'name' => $proker->nama_ketupel,
            'nim' => $proker->nim_ketupel,
            'ttd' => public_path('ttd') . '/' . $proker->ttd_ketupel
        ];

        if ($organisasi == 'BEM') {
            $html = view('pdf.signatures', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        } elseif (stripos($organisasi, 'UKM') !== false) {
            $html = view('pdf.ukm-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        } else {
            $html = view('pdf.hima-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        }

        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');

        // Membuat direktori jika belum ada
        $path = public_path('pengesahan');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        // Membuat nama file dengan UUID
        $fileName = Str::uuid() . '.pdf';
        $filePath = $path . '/' . $fileName;

        // Menyimpan PDF ke direktori public/pengesahan
        $pdf->save($filePath);

        // Menyimpan nama file di database
        $proposal->pengesahan = $fileName;
        $proposal->save();

        // Mengirim PDF ke browser untuk ditampilkan maupun diunduh
        return $pdf->stream('document.pdf');
    }

}
