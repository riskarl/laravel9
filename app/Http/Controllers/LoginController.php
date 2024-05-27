<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Session;

class LoginController extends Controller
{
    function signin(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $jabatan = $user->jabatan()->first();

            session(['user' => $user->only(['id', 'name', 'username', 'role', 'organization'])]);
            session(['jabatan' => $jabatan ? $jabatan->only(['jabatan_id', 'jabatan']) : null]);

            switch ($user->role) {
                case 1:
                    return redirect()->intended('/dashboard');
                case 2:
                    return redirect()->intended('/dashboard/organisasi');
                case 3:
                    return redirect()->intended('/dashboard/pengecek');
                case 4:
                    return redirect()->intended('/dashboard/bpm');
                default:
                    return redirect('/'); // default redirection
            }
        }

        return back()->with('loginError', 'Login Failed!');
    }

    function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect('/'); // Redirect ke halaman login setelah logout
    }
}
