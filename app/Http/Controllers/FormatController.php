<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Format;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Models\SetAnggaran;
use Carbon\Carbon;

class FormatController extends Controller
{
    public function index()
    {
        $format = Format::all();
        // Ambil data SetAnggaran terbaru
    $setAnggaran = SetAnggaran::orderBy('updated_at', 'desc')->first();
    if (!$setAnggaran) {
        session()->flash('error', 'Tidak ada data anggaran yang ditemukan.');
        return view('format', [
            'format' => collect([]) // Koleksi kosong jika tidak ada data
        ]);
    }

    // Ambil tanggal mulai periode dari data SetAnggaran
    $tglSetAnggaran = $setAnggaran->tgl_mulai_periode;
    if (!$tglSetAnggaran) {
        session()->flash('error', 'Tanggal mulai periode tidak ditemukan pada data anggaran.');
        return view('format', [
            'format' => collect([]) // Koleksi kosong jika tidak ada data
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
        session()->flash('error', 'Tidak ada data format yang berlaku untuk periode ini.');
        return view('format', [
            'format' => collect([]) // Koleksi kosong jika tidak ada data valid dalam rentang periode
        ]);
    }

    // Filter data Format yang berada dalam rentang periode aktif
    $format = Format::whereBetween('created_at', [$tglSetAnggaran, $endDate])->get();

        // Mengirim data pengguna ke view 'lihat-proposal'
        return view('format', ['format' => $format]);
    }

    public function indexformat()
    {
        $format = Format::all();
        // Mengirim data pengguna ke view 'lihat-proposal'
        return view('format-organisasi', ['format' => $format]);
    }


    public function store(Request $request)
    {
        // Validasi file
        $validatedData = $request->validate([
            'jenis_format' => 'required|string|max:500',
            'file_format' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Dapatkan file dari request
        $file = $request->file('file_format');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('format');

        // Cek dan buat direktori jika belum ada
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Pindahkan file ke direktori yang ditentukan
        $file->move($directory, $filename);

        // Ambil data SetAnggaran terbaru
        $setAnggaran = SetAnggaran::orderBy('updated_at', 'desc')->first();
        if (!$setAnggaran) {
            return redirect()->back()->with('error', 'Tidak ada data anggaran yang ditemukan.');
        }

        // Ambil tanggal mulai periode dari data SetAnggaran
        $tglSetAnggaran = $setAnggaran->tgl_mulai_periode;
        if (!$tglSetAnggaran) {
            return redirect()->back()->with('error', 'Tanggal mulai periode tidak ditemukan pada data anggaran.');
        }

        $periode = $setAnggaran->jenis_periode; // 'bulan' atau 'tahun'
        $total_periode = $setAnggaran->total_periode;

        // Menggunakan Carbon untuk mengatur tanggal akhir periode
        $endDate = $periode == 'bulan' 
            ? Carbon::parse($tglSetAnggaran)->addMonths($total_periode) 
            : Carbon::parse($tglSetAnggaran)->addYears($total_periode);

        // Tanggal dan waktu sekarang
        $currentDate = Carbon::now();

        // Memastikan tanggal saat ini berada dalam rentang periode yang sesuai (>= tanggal mulai dan <= tanggal akhir)
        if ($currentDate->lt(Carbon::parse($tglSetAnggaran)) || $currentDate->gt($endDate)) {
            return redirect()->back()->with('error', 'Tidak ada data anggaran yang berlaku untuk periode ini.');
        }

        try {
        // Menyimpan data ke dalam database
        Format::create([
            'jenis_format' => $validatedData['jenis_format'],
            'file_format' => $filename,
        ]);

        return redirect('/file-format')->with('success', 'Format berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Menyimpan Data Format. Silahkan Coba Lagi');
        }
    }

    public function update(Request $request, $id_format)
    {
        // Validasi input
        $validatedData = $request->validate([
            'jenis_format' => 'required|string|max:100',
            'file_format' => 'required|file|mimes:pdf,doc,docx',
        ]);

        // Temukan entri Format berdasarkan id
        $format = Format::find($id_format);

        if (!$format) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Jika ada file yang diunggah
        if ($request->hasFile('file_format')) {
            // Dapatkan file dari request
            $file = $request->file('file_format');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $directory = public_path('format');

            // Cek dan buat direktori jika belum ada
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Pindahkan file ke direktori yang ditentukan
            $file->move($directory, $filename);

            // Hapus file lama jika ada
            if ($format->file_format && File::exists($directory . '/' . $format->file_format)) {
                File::delete($directory . '/' . $format->file_format);
            }

            // Perbarui kolom file_format dengan nama file baru
            $format->file_format = $filename;
        }

        // Ambil data SetAnggaran terbaru
        $setAnggaran = SetAnggaran::orderBy('updated_at', 'desc')->first();
        if (!$setAnggaran) {
            return redirect()->back()->with('error', 'Tidak ada data anggaran yang ditemukan.');
        }

        // Ambil tanggal mulai periode dari data SetAnggaran
        $tglSetAnggaran = $setAnggaran->tgl_mulai_periode;
        if (!$tglSetAnggaran) {
            return redirect()->back()->with('error', 'Tanggal mulai periode tidak ditemukan pada data anggaran.');
        }

        $periode = $setAnggaran->jenis_periode; // 'bulan' atau 'tahun'
        $total_periode = $setAnggaran->total_periode;

        // Menggunakan Carbon untuk mengatur tanggal akhir periode
        $endDate = $periode == 'bulan' 
            ? Carbon::parse($tglSetAnggaran)->addMonths($total_periode) 
            : Carbon::parse($tglSetAnggaran)->addYears($total_periode);

        // Tanggal dan waktu sekarang
        $currentDate = Carbon::now();

        // Memastikan tanggal saat ini berada dalam rentang periode yang sesuai (>= tanggal mulai dan <= tanggal akhir)
        if ($currentDate->lt(Carbon::parse($tglSetAnggaran)) || $currentDate->gt($endDate)) {
            return redirect()->back()->with('error', 'Tidak ada data anggaran yang berlaku untuk periode ini.');
        }

        try {
        // Perbarui kolom jenis_format
        $format->jenis_format = $validatedData['jenis_format'];
        $format->save();

        return redirect('/file-format')->with('success', 'Format File berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Mengubah Data Format. Silahkan Coba Lagi');
        }
    }

    public function delete($id_format)
    {
        try {
        // Temukan entri Format berdasarkan id
        $format = Format::find($id_format);

        if (!$format) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $directory = public_path('format');

        // Hapus file jika ada
        if ($format->file_format && File::exists($directory . '/' . $format->file_format)) {
            File::delete($directory . '/' . $format->file_format);
        }

        // Hapus data dari database
        $format->delete();

        return redirect('/file-format')->with('success', 'Format berhasil dihapus!');
        } catch (\Exception $e) {
            // Handle error if any exception occurs
            return redirect()->back()->with('error', 'Gagal menghapus data format. Silakan coba lagi.');
        }
    }
}
