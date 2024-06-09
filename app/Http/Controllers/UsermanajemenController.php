<?php

namespace App\Http\Controllers;

use App\Models\Organisasi;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Config;
use Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class UsermanajemenController extends Controller
{
    function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:30',
            'username' => 'required|max:15|unique:users,username',
            'email' => 'required',
            'password' => 'required',
            'organization' => 'required',
            'jabatan_id' => 'required|exists:jabatan,jabatan_id',
            'role' => 'required',
            'code_id' => 'required', // Menambahkan validasi untuk jenis_id
            'number_id' => 'required', // Menambahkan validasi untuk nomer_id
            'ttd' => 'file|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Cek dan buat folder ttd jika belum ada
        $ttdPath = public_path('ttd');
        if (!File::exists($ttdPath)) {
            File::makeDirectory($ttdPath, 0755, true);
        }

        // Proses upload file TTD
        if ($request->hasFile('ttd')) {
            $ttdFile = $request->file('ttd');
            $ttdFilename = Str::uuid() . '.' . $ttdFile->getClientOriginalExtension();
            $ttdFile->move($ttdPath, $ttdFilename);

            // Tambahkan nama file TTD ke data yang divalidasi
            $validatedData['ttd'] = $ttdFilename;
        }

        User::create($validatedData);
        return redirect('/usermanajemen');
    }

    function create()
    {
        $users = User::with('jabatan')->get();

        return view('usermanajemen', ['users' => $users]);
    }

    function createform()
    {
        $jabatans = Jabatan::all();
        $organisasi = Organisasi::all();

        return view('usermanajemen-create', ['jabatans' => $jabatans, 'organisasi' => $organisasi]);
    }

    function edit(User $user)
    {
        $jabatans = Jabatan::all();
        $organisasi = Organisasi::all();
        return view('usermanajemen-edit', ['user' => $user, 'jabatans' => $jabatans, 'organisasi' => $organisasi]);
    }

    function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => "required|max:35",
            'username' => "required|max:15",
            'email' => 'required',
            'organization' => "required",
            'jabatan_id' => "required|exists:jabatan,jabatan_id",
            'role' => "required",
            'code_id' => 'required', // Menambahkan validasi untuk jenis_id
            'number_id' => 'required', // Menambahkan validasi untuk nomer_id
            'ttd' => 'file|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Cek dan buat folder ttd jika belum ada
        $ttdPath = public_path('ttd');
        if (!File::exists($ttdPath)) {
            File::makeDirectory($ttdPath, 0755, true);
        }

        // Proses upload file TTD
        if ($request->hasFile('ttd')) {
            $ttdFile = $request->file('ttd');
            $ttdFilename = Str::uuid() . '.' . $ttdFile->getClientOriginalExtension();
            $ttdFile->move($ttdPath, $ttdFilename);

            // Hapus file TTD lama jika ada
            if ($user->ttd && File::exists($ttdPath . '/' . $user->ttd)) {
                File::delete($ttdPath . '/' . $user->ttd);
            }

            // Tambahkan nama file TTD baru ke data yang divalidasi
            $validatedData['ttd'] = $ttdFilename;
        }

        // Update user dengan data yang divalidasi
        $user->update($validatedData);
        return redirect('/usermanajemen');
    }

    function delete($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect('/usermanajemen');
    }
}
