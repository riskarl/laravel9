<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Config;
use Session;

class LoginController extends Controller
{
    function signin(Request $request)
    { {
            $credentials = $request->validate([
                'username' => 'required',
                'password' => 'required'
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                if (Auth::user()->role == 1) {
                    return redirect()->intended('/dashboard');
                } else if (Auth::user()->role == 2) {
                    return redirect()->intended('/dashboard/organisasi');
                } else if (Auth::user()->role == 3) {
                    return redirect()->intended('/dashboard/pengecek');
                } else if (Auth::user()->role == 4) {
                    return redirect()->intended('/dashboard/bpm');
                }
            }

            return back()->with('loginError', 'Login Failed!');
        }
        // $user = DB::table('users')->where('username', $request->username)->first();
        // if ($user && $request->password == $user->password) {
        //     return redirect()->intended('dashboard');
        // }
        // return back()->withErrors([
        //     'username' => 'The provided credentials do not match our records.',
        // ]);
    }


    function logout()
    {
        Session::flush();
        return redirect('/'); // removes all session data
    }
}