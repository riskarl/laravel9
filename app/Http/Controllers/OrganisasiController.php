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
            'nama_organisasi' => 'required',
            'periode' => 'required',
        ]);
        Organisasi::create([
            'nama_organisasi' => $request->nama_organisasi,
            'periode' => $request->periode,
        ]);
        // Redirect dengan pesan sukses
        return redirect('/organisasi')->with('success', 'Data organisasi berhasil disimpan.');
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
    function update(Request $request, Organisasi $organisasi)
    {
        //validasi data berdasarkan request sebelum melakukan update
        //nama_organisasi dan periode tidak boleh kosong
        $result = $request->validate(['nama_organisasi' => "required", "periode" => "required"]);
        //query update, dengan $result berisi data yang sudah divalidasi dari request
        //update($result) mengubah sesuai id 
        Organisasi::where("id", $organisasi->id)->update($result);
        return redirect('/organisasi')->with('success', 'Data organisasi berhasil diupdate.');
    }

    //fungsi hapus data organisasi
    public function delete($id)
    {
        // Mengambil data organisasi berdasarkan ID yang diberikan
        $organisasi = Organisasi::find($id);
        // Menghapus data organisasi dari database
        $organisasi->delete();
        // Mengarahkan pengguna kembali ke halaman daftar organisasi
        return redirect('/organisasi')->with('success', 'Data organisasi berhasil dihapus.');
    }
}
