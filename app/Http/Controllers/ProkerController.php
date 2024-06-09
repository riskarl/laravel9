<?php

namespace App\Http\Controllers;

use App\Models\Organisasi;
use App\Models\Proker;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProkerController extends Controller
{
    
    public function index()
    {
        // Mendapatkan informasi pengguna saat ini
        $currentUser = $this->getCurrentUser();
        //mengambil semua data organisasi
        $organisasi = Organisasi::all();
        //mengambil semua data proker
        $listproker = Proker::all();
        //mendapatkan jabatan dari pengguna saat ini
        $jabatan = $currentUser['jabatan'];
        //mendapatkan organisasi dari pengguna saat ini
        $organisasiUser = $currentUser['organisasi'];
        return view('proker', ['listproker' => $listproker, 'organisasi' => $organisasi, 'jabatan' => $jabatan, 'orguser' => $organisasiUser]);
    }

    public function tampil()
    {
        //mendapatkan informasi pengguna saat ini
        $currentUser = $this->getCurrentUser();
        //mengambil semua data organisasi
        $organisasi = Organisasi::all();
        //mengambil semua data proker
        $listproker = Proker::all();
        //mendapatkan jabatan dari pengguna saat ini
        $jabatan = $currentUser['jabatan'];
        //mendapatkan informasi dari pengguna saat ini
        $organisasiUser = $currentUser['organisasi'];
        return view('proker-create', ['listproker' => $listproker, 'organisasi' => $organisasi, 'jabatan' => $jabatan, 'orguser' => $organisasiUser]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'nama_organisasi' => 'required',
            'nama_proker' => 'required',
            'nama_ketupel' => 'required',
            'nim_ketupel' => 'required',
            'tanggal' => 'required',
            'tempat' => 'required',
            'dana_diajukan' => 'required',
            'ttd_ketupel' => 'file|mimes:jpeg,png,jpg,gif|max:2048' // Menambahkan validasi untuk file ttd
        ]);

        // Cek dan buat folder ttd jika belum ada
        $ttdPath = public_path('ttd');
        if (!File::exists($ttdPath)) {
            File::makeDirectory($ttdPath, 0755, true);
        }

        // Proses upload file TTD
        if ($request->hasFile('ttd_ketupel')) {
            $ttdFile = $request->file('ttd_ketupel');
            $ttdFilename = Str::uuid() . '.' . $ttdFile->getClientOriginalExtension();
            $ttdFile->move($ttdPath, $ttdFilename);

            // Tambahkan nama file TTD ke data yang divalidasi
            $validatedData['ttd_ketupel'] = $ttdFilename;
        }
        $validatedData['id_organisasi'] = $validatedData['nama_organisasi'];

        // Simpan data ke database
        Proker::create($validatedData);
        return redirect('/proker');
    }

    public function edit(Proker $proker)
    {
        //mendapatkan informasi pengguna saat ini
        $currentUser = $this->getCurrentUser();
        //mengambil semua data organisasi
        $organisasi = Organisasi::all();
        //mendapatkan organisasi dari pengguna saat ini
        $organisasiUser = $currentUser['organisasi'];
        return view('proker-edit', ['proker' => $proker, 'organisasi' => $organisasi, 'orguser' => $organisasiUser]);
    }

    public function update(Request $request, Proker $proker)
    {
        $validatedData = $request->validate([
            'nama_organisasi' => "required",
            "nama_proker" => "required",
            "nama_ketupel" => "required",
            'nim_ketupel' => 'required',
            "tanggal" => "required",
            "tempat" => "required",
            "dana_diajukan" => "required",
            "ttd_ketupel" => "file|mimes:jpeg,png,jpg,gif|max:2048" // Menambahkan validasi untuk file ttd
        ]);

        // Cek dan buat folder ttd jika belum ada
        $ttdPath = public_path('ttd');
        if (!File::exists($ttdPath)) {
            File::makeDirectory($ttdPath, 0755, true);
        }

        // Proses upload file TTD
        if ($request->hasFile('ttd_ketupel')) {
            $ttdFile = $request->file('ttd_ketupel');
            $ttdFilename = Str::uuid() . '.' . $ttdFile->getClientOriginalExtension();
            $ttdFile->move($ttdPath, $ttdFilename);

            // Hapus file TTD lama jika ada
            if ($proker->ttd_ketupel && File::exists($ttdPath . '/' . $proker->ttd_ketupel)) {
                File::delete($ttdPath . '/' . $proker->ttd_ketupel);
            }

            // Tambahkan nama file TTD baru ke data yang divalidasi
            $validatedData['ttd_ketupel'] = $ttdFilename;
        }
        $validatedData['id_organisasi'] = $validatedData['nama_organisasi'];

        // Update proker dengan data yang divalidasi
        $proker->update($validatedData);
        return redirect('/proker');
    }

    function delete($id)
    {
        $proker = Proker::find($id);
        $proker->delete();
        return redirect('/proker');
    }
}
