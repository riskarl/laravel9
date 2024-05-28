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

    protected function getCurrentUser()
    {
        $user =Session::get('user');
        $jabatan = Session::get('jabatan');

        if ($jabatan) {
            $user['jabatan'] = $jabatan['jabatan'];
            $user['jabatan_id'] = $jabatan['jabatan_id'];
        } else {
            $user['jabatan'] = null;
            $user['jabatan_id'] = null;
        }

        $user['organisasi'] = $user['organization'];
        unset($user['organization']);

        return $user;
    }

    public function generatePdfWithSignatures(array $signatures)
    {
        $html = view('pdf.signatures', compact('signatures'))->render();

        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');
        return $pdf->stream('document.pdf');
    }
}
