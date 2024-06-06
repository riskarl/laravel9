<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ResetPasswordController extends Controller
{
    // Menampilkan form untuk memasukkan email
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }
    public function showreset()
    {
        return view('auth.forgot-password');
    }

    // Mengirimkan email reset password
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }
    // Menampilkan form untuk reset password
    public function showResetForm($token)
    {
        return view('auth.passwords.reset')->with(['token' => $token]);
    }

    // Menangani proses reset password
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );
        return $status === Password::PASSWORD_RESET
            ? redirect()->to('/')->with('status', __($status))
            : back()->withErrors(['email' => [($status)]]);
    }
}
