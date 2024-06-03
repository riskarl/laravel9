<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class Controller extends BaseController
{
    //melakukan tindakan tertentu dan memvalidasi input
    use AuthorizesRequests, ValidatesRequests;

    //deklarasi fungsi, protected hanya dapat diakses oleh kelasnya sendiri dan kelas turunannya
    protected function getCurrentUser()
    {
        //mengambil data 'user' dan data 'jabatan' menggunakan Session::get
        $user = Session::get('user');
        $jabatan = Session::get('jabatan');

        //pengecekan data jabatan, jika ada data jabatan, jabatan_id, code_jabatan dimasukkan ke array $user. 
        //jika tidak ada maka nilai jabatan, jabatan_id, code_jabatan di set null dalam array $user
        if ($jabatan) {
            $user['jabatan'] = $jabatan['jabatan'];
            $user['jabatan_id'] = $jabatan['jabatan_id'];
            $user['code_jabatan'] = $jabatan['code_jabatan'];
        } else {
            $user['jabatan'] = null;
            $user['jabatan_id'] = null;
            $user['code_jabatan'] = null;
        }

        //mengubah nama kunci organization menjadi organisasi dan menghapus nama kunci organization
        $user['organisasi'] = $user['organization'];
        unset($user['organization']);

        //mengembalikan data pengguna yang telah di format
        return $user;
    }

    //fungsi bersifat public dan menerima satu parameter berupa array $signatures
    public function generatePdfWithSignatures(array $signatures, $namaKegiatan, $organisasi = null, array $ketupel = [] )
    {
        //mengecek apakah variabel organisasi bernilai bem, jika kondisi terpenuhi maka variabel html akan diisi dengan
        //render hasil pdf.signatures dengan data sigantures, namaKegiatan, Ketupel
        if ($organisasi == 'BEM') {
            $html = view('pdf.signatures', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        //mengecek ukm berada pada variabel organisasi dengan fungsi strpos mengembalikan posisi string 'ukm'
        //pertama kali muncul dalam sorganisasi atau false jika tidak ditemukan
        }elseif (strpos($organisasi, 'UKM') !== false) {
            $html = view('pdf.ukm-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        }else {
            $html = view('pdf.hima-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        }
        
        // Memuat / konversi HTML yang telah dirender menjadi PDF, dengan ukuran kertas A4 dan orientasi portrait
        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');
        // Mengirim PDF ke browser untuk ditampilkan maupun diunduh
        return $pdf->stream('document.pdf');
    }
    
}
