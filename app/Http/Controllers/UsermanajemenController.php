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
        $result = $request->validate(['name' => "required|max:35", "username" => "required|max:15", "password" => "required", "organization" => "required", "id_jabatan" => "required", "role" => "required"]);
        User::create($result);
        return redirect('/usermanajemen');
    }

    function create()
    {
        $users = User::all();
        $jabatans = Jabatan::all();

        return view('usermanajemen', ['users' => $users, 'jabatans' => $jabatans]);
    }

    function createform()
    {
        $jabatans = Jabatan::all();

        return view('usermanajemen-create', ['jabatans' => $jabatans]);
    }

    function update(Request $request, User $user)
    {
        $result = $request->validate(['name' => "required|max:35", "username" => "required|max:15", "organization" => "required", "jabatan" => "required", "role" => "required"]);
        User::where("id", $user->id)->update($result);
        return redirect('/usermanajemen');
    }

    function delete($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect('/usermanajemen');
    }
}