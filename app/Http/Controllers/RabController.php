<?php

namespace App\Http\Controllers;

use App\Models\Proker;
use App\Models\Rab;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Models\SetAnggaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class RabController extends Controller
{
    public function index()
    {
        $currentUser = $this->getCurrentUser();
        $organisasiUser = $currentUser['organisasi'];

        // Dapatkan data SetAnggaran terbaru
        $setAnggaran = SetAnggaran::orderBy('updated_at', 'desc')->first();
        if (!$setAnggaran) {
            Session::flash('error', 'Tidak ada data anggaran yang ditemukan.');
            return view('upload-rab', [
                'listproker' => collect([]), // Koleksi kosong jika tidak ada data
                'orguser' => $organisasiUser,
            ]);
        }

        // Ambil tanggal mulai periode dari data SetAnggaran
        $tglSetAnggaran = $setAnggaran->tgl_mulai_periode;
        if (!$tglSetAnggaran) {
            Session::flash('error', 'Tanggal mulai periode tidak ditemukan pada data anggaran.');
            return view('upload-rab', [
                'listproker' => collect([]), // Koleksi kosong jika tidak ada data
                'orguser' => $organisasiUser,
            ]);
        }

        $periode = $setAnggaran->jenis_periode; // 'bulan' atau 'tahun'
        $total_periode = $setAnggaran->total_periode;

        // Menggunakan Carbon untuk mengatur tanggal akhir periode
        $endDate = $periode == 'bulan' 
            ? Carbon::parse($tglSetAnggaran)->addMonths($total_periode) 
            : Carbon::parse($tglSetAnggaran)->addYears($total_periode);

        // Tanggal dan waktu sekarang
        $currentDate = Carbon::now();

        // Memastikan kita berada dalam rentang periode yang sesuai (>= tanggal mulai dan <= tanggal akhir)
        if ($currentDate->lt(Carbon::parse($tglSetAnggaran)) || $currentDate->gt($endDate)) {
            Session::flash('error', 'Tidak ada data proker yang berlaku untuk periode ini.');
            return view('upload-rab', [
                'listproker' => collect([]), // Koleksi kosong jika tidak ada data valid dalam rentang periode
                'orguser' => $organisasiUser,
            ]);
        }

        // Query data Proker yang berada dalam rentang waktu yang berjalan
        $proker = Proker::with('rab')
                        ->whereBetween('created_at', [$tglSetAnggaran, $endDate])
                        ->get();

        // Mengirim data pengguna ke view 'upload-rab'
        return view('upload-rab', [
            'listproker' => $proker,
            'orguser' => $organisasiUser,
        ]);
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

    public function uploadrab(Request $request)
    {
        $validatedData = $request->validate([
            'file_rab' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'id_proker' => 'required',
        ]);

        $file = $request->file('file_rab');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('rab');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Cek apakah RAB sudah ada berdasarkan id_proker
        $existingRab = Rab::where('id_proker', $validatedData['id_proker'])->first();

        if ($existingRab) {
            // Jika ada, hapus file lama
            $oldFile = $directory . '/' . $existingRab->file_rab;
            if (File::exists($oldFile)) {
                File::delete($oldFile);
            }

            // Update record dengan file baru
            $existingRab->file_rab = $filename;
            $existingRab->save();
        } else {
            // Jika tidak ada, buat record baru
            $rab = new Rab();
            $rab->id_proker = $validatedData['id_proker'];
            $rab->file_rab = $filename;
            $rab->save();
        }

        // Pindahkan file baru ke directory
        $file->move($directory, $filename);

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
