<?php

namespace App\Http\Controllers;

use App\Models\Rab;
use Illuminate\Http\Request;
use App\Models\LPJ;
use App\Models\Proposal;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('files');

        // Membuat direktori jika tidak ada
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Handle file baru
        if ($request->hasFile('file')) {
            // Pindahkan file baru ke direktori
            $file->move($directory, $filename);
        }

        // Cek apakah ini update atau penambahan baru
        if (!empty($request->existing_file_name)) {
            // Ini adalah update file, temukan proposal yang ada
            $proposal = Proposal::where('id_proker', $request->id_proker)->first();
            if ($proposal && File::exists($directory . '/' . $proposal->file_proposal)) {
                // Hapus file lama
                File::delete($directory . '/' . $proposal->file_proposal);
            }
            $proposal->file_proposal = $filename;
            $proposal->status_flow = 0;
            $proposal->status = 'Pending';
            $proposal->catatan = 'Belum ada catatan';
        } else {
            // Ini adalah penambahan baru
            $proposal = new Proposal();
            $proposal->file_proposal = $filename;
            $proposal->status = 'Pending';
            $proposal->catatan = 'Belum ada catatan';
            $proposal->id_proker = $request->id_proker;
            $proposal->status_flow = 0;
        }

        // Simpan perubahan atau penambahan baru
        $proposal->save();

        return redirect()->back()->with('success', 'File Proposal berhasil diupload!');
    }

    public function uploadrab(Request $request)
    {
        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('rab');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file->move($directory, $filename);

        $rab = new Rab();
        $rab->file_rab = $filename;
        $rab->id_proker = $request->id_proker;
        $rab->save();

        return redirect()->back()->with('success', 'File RAB berhasil diupload!');
    }
    
    public function uploadlpj(Request $request)
    {
        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('lpj');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Handle file baru
        if ($request->hasFile('file')) {
            // Pindahkan file baru ke direktori
            $file->move($directory, $filename);
        }

        // Cek apakah ini update atau penambahan baru
        if (!empty($request->existing_file_name)) {
            // Ini adalah update file, temukan LPJ yang ada
            $lpj = LPJ::where('id_proker', $request->id_proker)->first();
            if ($lpj && File::exists($directory . '/' . $lpj->file_lpj)) {
                // Hapus file lama
                File::delete($directory . '/' . $lpj->file_lpj);
            }
            $lpj->file_lpj = $filename;
            $lpj->status = 'Pending';
            $lpj->catatan = 'Belum ada catatan';
        } else {
            // Ini adalah penambahan baru
            $lpj = new LPJ();
            $lpj->file_lpj = $filename;
            $lpj->status = 'Pending';
            $lpj->catatan = 'Belum ada catatan';
            $lpj->id_proker = $request->id_proker;
        }

        // Simpan perubahan atau penambahan baru
        $lpj->save();

        return redirect()->back()->with('success', 'File LPJ berhasil diupload!');
    }
}
