<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Session;

class LoginController extends Controller
{
    //fungsi memiliki parameter $request dr laravel yang mewakili permintaan http masuk
    function signin(Request $request)
    {
        //memvalidasi username dan password
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        //fungsi Auth::attempt autentikasi pengguna dengan kredensial yang divalidasi
        //jika autentikasi berhasil maka kode if akan dijalankan
        if (Auth::attempt($credentials)) {
            //jika autentikasi berhasil maka session akan diregenerasi untuk mencegah fixation session
            $request->session()->regenerate();

            //mengambil data pengguna yang sedang diautentikasi dan mengambil data jabatan pertama yg terkait dgn pengguna tsb
            $user = Auth::user();
            $jabatan = $user->jabatan()->first();

            //user disimpan hanya dengan atribut tertentu = id, name, username, role, organization
            session(['user' => $user->only(['id', 'name', 'username', 'role', 'organization'])]);
            //jabatan disimpan jika ada, atau null jika tidak ada
            session(['jabatan' => $jabatan ? $jabatan->only(['jabatan_id', 'jabatan']) : null]);

            //mengalihkan peran berdasarkan role
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
        //jika kredensial salah dan autentikasi gagal, akan diarahkan ke halaman login lagi dan ada pesan 
        //dengan nama kunci loginError
        return back()->with('loginError', 'Username atau Password Anda Salah!');
    }

    function logout()
    {
        //menghapus semua data dari sesi
        Session::flush();
        //mengeluarkan pengguna yang sedang login dari sistem autentikasi
        Auth::logout();
        // Redirect ke halaman login setelah logout
        return redirect('/');
    }
}
