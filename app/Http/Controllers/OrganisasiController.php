<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organisasi;

class OrganisasiController extends Controller
{
    function create()
    {
        return view('usermanajemen');
    }
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
        return redirect('/organisasi');
    }

    public function index()
    {
        $listorganisasi = Organisasi::all();
        return view('organisasi', ['listorganisasi' => $listorganisasi]);
    }


    function update(Request $request, Organisasi $organisasi)
    {
        $result = $request->validate(['nama_organisasi' => "required", "periode" => "required"]);
        Organisasi::where("id", $organisasi->id)->update($result);
        return redirect('/organisasi');
    }

    function delete($id)
    {
        $organisasi = Organisasi::find($id);
        $organisasi->delete();
        return redirect('/organisasi');
    }
}
