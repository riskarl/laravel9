<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Jabatan; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Config;
use Session;

class UsermanajemenController extends Controller
{
    function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:35',
            'username' => 'required|max:15|unique:users,username',
            'password' => 'required',
            'organization' => 'required',
            'jabatan_id' => 'required|exists:jabatan,jabatan_id',
            'role' => 'required'
        ]);
        
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

        return view('usermanajemen-create', ['jabatans' => $jabatans]);
    }

    function edit(User $user)
    {
        $jabatans = Jabatan::all();
        return view('usermanajemen-edit', ['user' => $user, 'jabatans' => $jabatans]);
    }


    function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => "required|max:35",
            'username' => "required|max:15",
            'organization' => "required",
            'jabatan_id' => "required|exists:jabatan,jabatan_id",
            'role' => "required"
        ]);

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