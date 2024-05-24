<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Config;
use Session;

class UsermanajemenController extends Controller
{
    function store(Request $request)
    {
        $result = $request->validate(['name' => "required|max:35", "username" => "required|max:15", "password" => "required", "organization" => "required", "jabatan" => "required", "role" => "required"]);
        User::create($result);
        return redirect('/usermanajemen');
    }

    function create()
    {
        $user = User::all();
        return view('usermanajemen', ['users' => $user]);
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