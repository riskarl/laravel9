<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    //deklarasi fungsi, protected hanya dapat diakses oleh kelasnya sendiri dan kelas turunannya
    protected function getCurrentUser()
    {
        //mengambil data 'user' dan data 'jabatan' menggunakan Session::get
        $user = Session::get('user');
        $jabatan = Session::get('jabatan');

        //pengecekan data jabatan, jika ada data jabatan dan jabatan_id dimasukkan ke array $user. 
        //jika tidak ada maka nilai jabatan dan jabatan_id di set null dalam array $user
        if ($jabatan) {
            $user['jabatan'] = $jabatan['jabatan'];
            $user['jabatan_id'] = $jabatan['jabatan_id'];
        } else {
            $user['jabatan'] = null;
            $user['jabatan_id'] = null;
        }

        //mengubah nama kunci organization menjadi organisasi dan menghapus nama kunci organization
        $user['organisasi'] = $user['organization'];
        unset($user['organization']);

        //mengembalikan data pengguna yang telah di format
        return $user;
    }

    //fungsi bersifat public dan menerima satu parameter berupa array $signatures
    public function generatePdfWithSignatures(array $signatures)
    {
        //membuat tampilan html dengan template pdf.sigantures dan data yang ada dalam array $signatures
        //compact('sigantures') membuat array asosiatif dari variabel $signatures
        //render untuk mengubah view menjadi string html
        $html = view('pdf.signatures', compact('signatures'))->render();

        //memuat /konversi html yang telah dirender menjadi pdf, dengan uk kertas A4 dan potrait
        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');
        //mengirim pdf ke browser untuk ditampilkan maupun diunduh
        return $pdf->stream('document.pdf');
    }
}
