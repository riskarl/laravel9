<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }


        $file->move($directory, $filename);

        $proposal = new Proposal();
        $proposal->file_proposal = $filename;
        $proposal->status = 'Pending';
        $proposal->catatan = 'Belum ada catatan';
        $proposal->id_proker = $request->id_proker;
        $proposal->save();

        return redirect()->back()->with('success', 'File berhasil diupload!');
    }
}
