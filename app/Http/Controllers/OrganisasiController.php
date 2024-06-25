<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organisasi;

class OrganisasiController extends Controller
{
    //menampilkan tabel di view usermanajemen
    function create()
    {
        return view('usermanajemen');
    }

    //fungsi tambah data organisasi
    public function store(Request $request)
    {
    // Validasi input
    $request->validate([
        'nama_organisasi' => 'required|string|max:30',
        'periode' => 'required|integer|digits:4',
    ]);

    try {
        Organisasi::create([
            'nama_organisasi' => $request->nama_organisasi,
            'periode' => $request->periode,
        ]);

        // Redirect dengan pesan sukses
        return redirect('/organisasi')->with('success', 'Data organisasi berhasil disimpan.');
    } catch (\Exception $e) {
        // Handle error if any exception occurs
        return redirect()->back()->with('error', 'Gagal menyimpan data organisasi. Silakan coba lagi.');
    }
}


    //menampilkan semua data dalam model Organisasi
    public function index()
    {
        //mengambil semua data
        $listorganisasi = Organisasi::all();
        //data yang diambil dari database $listorganisasi dikirim ke view organisasi sbg variabel 'listorganisasi'
        return view('organisasi', ['listorganisasi' => $listorganisasi]);
    }


    //fungsi ubah data organisasi
    public function update(Request $request, Organisasi $organisasi)
{
    // Validasi input
    $request->validate([
        'nama_organisasi' => 'required|string|max:30',
        'periode' => 'required|integer|digits:4',
    ]);

    try {
        // Lakukan update data organisasi
        $organisasi->update([
            'nama_organisasi' => $request->nama_organisasi,
            'periode' => $request->periode,
        ]);

        // Redirect dengan pesan sukses
        return redirect('/organisasi')->with('success', 'Data organisasi berhasil diupdate.');
    } catch (\Exception $e) {
        // Handle error if any exception occurs
        return redirect()->back()->with('error', 'Gagal mengupdate data organisasi. Silakan coba lagi.');
    }
    }


    //fungsi hapus data organisasi
    public function delete($id)
{
    try {
        // Mengambil data organisasi berdasarkan ID yang diberikan
        $organisasi = Organisasi::findOrFail($id);

        // Menghapus data organisasi dari database
        $organisasi->delete();

        // Mengarahkan pengguna kembali ke halaman daftar organisasi
        return redirect('/organisasi')->with('success', 'Data organisasi berhasil dihapus.');
    } catch (\Exception $e) {
        // Handle error if any exception occurs
        return redirect()->back()->with('error', 'Gagal menghapus data organisasi. Silakan coba lagi.');
    }
}
}
