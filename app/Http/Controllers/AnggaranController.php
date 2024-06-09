<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\Organisasi;
use Illuminate\Http\Request;

class AnggaranController extends Controller
{
    public function index()
    {
        $anggaran = Anggaran::with('organisasi')->get();
        $organisasis = Organisasi::All();

        return view('anggaran-bpm', ['anggaran' => $anggaran, 'organisasis' => $organisasis]);
    }

    public function indexanggaranorganisasi()
    {
        $anggaran = Anggaran::with('organisasi')->get();

        return view('anggaran-organisasi', ['anggaran' => $anggaran]);
    }

    public function store(Request $request)
    {
        // Validasi input jika diperlukan
        $validatedData = $request->validate([
           'id_organisasi' => 'required|exists:tb_organisasi,id',
            'jumlah_mhs' => 'required|numeric',
            'jumlah_anggaran' => 'required|numeric',
            'total_anggaran' => 'required|numeric',
        ]);

        $organisasi = Organisasi::find($request->id);
        // Simpan data ke dalam database
        $anggaran = new Anggaran();
        $anggaran->id_organisasi = $request->id_organisasi;
        $anggaran->jumlah_mhs = $request->jumlah_mhs;
        $anggaran->jumlah_anggaran = $request->jumlah_anggaran;
        $anggaran->total_anggaran = $request->total_anggaran;
        $anggaran->save();

        // Berhasil, kirim respon
        return redirect('/anggaran')->with('success', 'Data Anggaran berhasil Disimpan!');
    }

    public function update(Request $request, $id)
    {
    // Validasi input jika diperlukan
    $validatedData = $request->validate([
        'id_organisasi' => 'required|exists:tb_organisasi,id',
        'jumlah_mhs' => 'required|numeric',
        'jumlah_anggaran' => 'required|numeric',
        'total_anggaran' => 'required|numeric',
    ]);

    // Cari data anggaran berdasarkan id
    $anggaran = Anggaran::find($id);

    if (!$anggaran) {
        // Jika data tidak ditemukan, kirim respon error
        return redirect('/anggaran')->with('error', 'Data Anggaran tidak ditemukan!');
    }

    // Update data di dalam database
    $anggaran->id_organisasi = $request->id_organisasi;
    $anggaran->jumlah_mhs = $request->jumlah_mhs;
    $anggaran->jumlah_anggaran = $request->jumlah_anggaran;
    $anggaran->total_anggaran = $request->total_anggaran;
    $anggaran->save();

    // Berhasil, kirim respon
    return redirect('/anggaran')->with('success', 'Data Anggaran berhasil diupdate!');
    }

    public function delete($id)
    {
        try {
            // Cari data anggaran berdasarkan id
            $anggaran = Anggaran::findOrFail($id);

            // Hapus data anggaran
            $anggaran->delete();

            // Redirect dengan pesan sukses
            return redirect('/anggaran')->with('success', 'Data anggaran berhasil dihapus!');
        } catch (\Exception $e) {
            // Redirect dengan pesan error jika terjadi kesalahan
            return redirect('/anggaran')->with('error', 'Terjadi kesalahan saat menghapus data anggaran');
        }
    }

}
