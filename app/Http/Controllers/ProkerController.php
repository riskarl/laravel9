<?php

namespace App\Http\Controllers;

use App\Models\Organisasi;
use App\Models\Proker;
use Illuminate\Http\Request;
use Session;

class ProkerController extends Controller
{
    public function index()
    {
        $organisasi = Organisasi::all();
        $listproker = Proker::all();
        $jabatan = Session::get('jabatan')['jabatan'];
        $organisasiUser = Session::get('user')['organization'];
        return view('proker', ['listproker' => $listproker, 'organisasi' => $organisasi, 'jabatan' => $jabatan, 'orguser' => $organisasiUser]);
    }

    public function tampil()
    {
        $organisasi = Organisasi::all();
        $listproker = Proker::all();
        $jabatan = Session::get('jabatan')['jabatan'];
        $organisasiUser = Session::get('user')['organization'];
        return view('proker-create', ['listproker' => $listproker, 'organisasi' => $organisasi, 'jabatan' => $jabatan, 'orguser' => $organisasiUser]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_organisasi' => 'required',
            'nama_proker' => 'required',
            'nama_ketupel' => 'required',
            'tanggal' => 'required',
            'tempat' => 'required',
            'dana_diajukan' => 'required',
        ]);
        Proker::create([
            'nama_organisasi' => $request->nama_organisasi,
            'nama_proker' => $request->nama_proker,
            'nama_ketupel' => $request->nama_ketupel,
            'tanggal' => $request->tanggal,
            'tempat' => $request->tempat,
            'dana_diajukan' => $request->dana_diajukan,
        ]);
        return redirect('/proker');
    }
    public function edit(Proker $proker)
    {
        $organisasi = Organisasi::all();
        $organisasiUser = Session::get('user')['organization'];
        return view('proker-edit', ['proker' => $proker, 'organisasi' => $organisasi, 'orguser' => $organisasiUser]);
    }


    public function update(Request $request, Proker $proker)
    {
        $result = $request->validate([
            'nama_organisasi' => "required",
            "nama_proker" => "required",
            "nama_ketupel" => "required",
            "tanggal" => "required",
            "tempat" => "required",
            "dana_diajukan" => "required"
        ]);

        $proker->update($result);
        return redirect('/proker');
    }
    function delete($id)
    {
        $proker = Proker::find($id);
        $proker->delete();
        return redirect('/proker');
    }
}
