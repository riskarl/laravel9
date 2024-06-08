<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Format;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class FormatController extends Controller
{
    public function index()
    {
        $format = Format::all();
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
            'jenis_format' => 'required|string|max:100',
            'file_format' => 'required|file|mimes:pdf,doc,docx',
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

        // Menyimpan data ke dalam database
        Format::create([
            'jenis_format' => $validatedData['jenis_format'],
            'file_format' => $filename,
        ]);

        return redirect('/file-format')->with('success', 'Format berhasil ditambahkan!');
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

        // Perbarui kolom jenis_format
        $format->jenis_format = $validatedData['jenis_format'];
        $format->save();

        return redirect('/file-format')->with('success', 'Format File berhasil diperbarui!');
    }

    public function delete($id_format)
    {
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
    }
}
