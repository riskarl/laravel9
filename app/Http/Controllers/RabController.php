<?php

namespace App\Http\Controllers;

use App\Models\Proker;
use App\Models\Rab;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

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
        $currentUser = $this->getCurrentUser();
        $proker = Proker::with('rab')->get();
        $organisasiUser = $currentUser['organisasi'];
        // Mengirim data pengguna ke view 'unduh-srpd'
        return view('unduh-srpd', ['listproker' => $proker, 'orguser' => $organisasiUser]);
    }

    public function uploadsrpd()
    {
        // $rab = Rab::find();
        $proker = Proker::with(['organisasi', 'rab', 'srpd'])->get();
        // Mengirim data pengguna ke view 'pengecekan-rab'
        return view('pengecekan-rab', ['listproker' => $proker]);
    }

    public function uploadrab(Request $request, $id)
    {
        $request->validate([
            'file_rab' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $file = $request->file('file_rab');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('rab');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file->move($directory, $filename);

        $rab = Rab::find($id);

        if (!$rab) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Update kolom file_rab
        $rab->file_rab = $filename;
        $rab->save();


        return redirect()->back()->with('success', 'File RAB berhasil diupload!');
    }

    public function upsrpd(Request $request, $id)
    {
        // Validasi file
        $request->validate([
            'file_srpd' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Dapatkan file dari request
        $file = $request->file('file_srpd');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('srpd');

        // Cek dan buat direktori jika belum ada
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Pindahkan file ke direktori yang ditentukan
        $file->move($directory, $filename);

        // Temukan entri Rab berdasarkan id
        $rab = Rab::find($id);

        if (!$rab) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Update kolom file_srpd
        $rab->file_srpd = $filename;
        $rab->save();

        return redirect()->back()->with('success', 'File SRPD berhasil diupload!');
    }
}
