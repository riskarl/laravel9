<?php

namespace App\Http\Controllers;

use App\Models\Proker;
use App\Models\LPJ;
use Illuminate\Http\Request;
use DB;
use Session;
use App\Models\MappingCheckLpj;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SetAnggaran;
use Carbon\Carbon;
use App\Models\User;

class LpjController extends Controller
{
    public function index()
    {
        $currentUser = $this->getCurrentUser();
        $organisasiUser = $currentUser['organisasi'];
        $lpj = LPJ::all();

        // Ambil data SetAnggaran terbaru
        $setAnggaran = SetAnggaran::orderBy('updated_at', 'desc')->first();
        if (!$setAnggaran) {
            session()->flash('error', 'Tidak ada data anggaran yang ditemukan.');
            return view('upload-lpj', [
                'listproker' => collect([]),
                'orguser' => $organisasiUser,
                'lpj' => $lpj
            ]);
        }

        // Ambil tanggal mulai periode dari data SetAnggaran
        $tglSetAnggaran = $setAnggaran->tgl_mulai_periode;
        if (!$tglSetAnggaran) {
            session()->flash('error', 'Tanggal mulai periode tidak ditemukan pada data anggaran.');
            return view('upload-lpj', [
                'listproker' => collect([]),
                'orguser' => $organisasiUser,
                'lpj' => $lpj
            ]);
        }

        $periode = $setAnggaran->jenis_periode; // 'bulan' atau 'tahun'
        $total_periode = $setAnggaran->total_periode;

        // Menggunakan Carbon untuk mengatur tanggal akhir periode
        $endDate = $periode == 'bulan' 
            ? Carbon::parse($tglSetAnggaran)->addMonths($total_periode)
            : Carbon::parse($tglSetAnggaran)->addYears($total_periode);

        // Tanggal dan waktu sekarang
        $currentDate = Carbon::now();

        // Memastikan kita berada dalam rentang periode yang sesuai (>= tanggal mulai dan <= tanggal akhir)
        if ($currentDate->lt(Carbon::parse($tglSetAnggaran)) || $currentDate->gt($endDate)) {
            session()->flash('error', 'Tidak ada data proker yang berlaku untuk periode ini.');
            return view('upload-lpj', [
                'listproker' => collect([]),
                'orguser' => $organisasiUser,
                'lpj' => $lpj
            ]);
        }

        // Filter data Proker yang berada dalam rentang periode aktif
        $proker = Proker::with(['lpj', 'proposal'])
            ->whereHas('proposal', function ($query) {
                $query->where('status_flow', 9);
            })
            ->whereBetween('created_at', [$tglSetAnggaran, $endDate])
            ->get();

        // Iterasi melalui setiap proker untuk memodifikasi nilai pengesahan
        foreach ($proker as $item) {
            if ($item->lpj && $item->lpj->status_flow_lpj != 9) {
                $item->proposal->pengesahan = 'File tidak ada';
            }
        }

        return view('upload-lpj', [
            'listproker' => $proker,
            'orguser' => $organisasiUser,
            'lpj' => $lpj
        ]);
    }



    public function indexlpj()
    {
        return view('lihat-lpj');
    }

    public function pengecekanlpj()
    {
        $currentUser = $this->getCurrentUser();
        $organisasiUser = $currentUser['organisasi'];
        $codeJabatan = $currentUser['code_jabatan'];
    
        // Ambil data SetAnggaran terbaru
        $setAnggaran = SetAnggaran::orderBy('updated_at', 'desc')->first();
        if (!$setAnggaran) {
            session()->flash('error', 'Tidak ada data anggaran yang ditemukan.');
            return view('pengecekan-lpj', [
                'listproker' => collect([]), // Koleksi kosong jika tidak ada data
                'orguser' => $organisasiUser,
                'codeJabatan' => $codeJabatan
            ]);
        }
    
        // Ambil tanggal mulai periode dari data SetAnggaran
        $tglSetAnggaran = $setAnggaran->tgl_mulai_periode;
        if (!$tglSetAnggaran) {
            session()->flash('error', 'Tanggal mulai periode tidak ditemukan pada data anggaran.');
            return view('pengecekan-lpj', [
                'listproker' => collect([]), // Koleksi kosong jika tidak ada data
                'orguser' => $organisasiUser,
                'codeJabatan' => $codeJabatan
            ]);
        }
    
        $periode = $setAnggaran->jenis_periode; // 'bulan' atau 'tahun'
        $total_periode = $setAnggaran->total_periode;
    
        // Menggunakan Carbon untuk mengatur tanggal akhir periode
        $endDate = $periode == 'bulan' 
            ? Carbon::parse($tglSetAnggaran)->addMonths($total_periode)
            : Carbon::parse($tglSetAnggaran)->addYears($total_periode);
    
        // Tanggal dan waktu sekarang
        $currentDate = Carbon::now();
    
        // Memastikan kita berada dalam rentang periode yang sesuai (>= tanggal mulai dan <= tanggal akhir)
        if ($currentDate->lt(Carbon::parse($tglSetAnggaran)) || $currentDate->gt($endDate)) {
            session()->flash('error', 'Tidak ada data proker yang berlaku untuk periode ini.');
            return view('pengecekan-lpj', [
                'listproker' => collect([]), // Koleksi kosong jika tidak ada data valid dalam rentang periode
                'orguser' => $organisasiUser,
                'codeJabatan' => $codeJabatan
            ]);
        }
    
        // Mengambil data proker dengan organisasi dan lpj terkait yang berada dalam rentang periode aktif
        $proker = Proker::with(['organisasi', 'lpj'])
            ->whereBetween('created_at', [$tglSetAnggaran, $endDate])
            ->get();
    
        // Mengirim data pengguna ke view 'pengecekan-lpj'
        return view('pengecekan-lpj', [
            'listproker' => $proker,
            'orguser' => $organisasiUser,
            'codeJabatan' => $codeJabatan
        ]);
    }    

    public function pengecekanlpjbpm()
    {
        return view('pengecekanlpj-bpm');
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'file_lpj' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'dana_disetujui' => 'required',
            'id_proker' => 'required',
        ]);

        // Ambil data SetAnggaran terbaru
        $setAnggaran = SetAnggaran::orderBy('updated_at', 'desc')->first();
        if (!$setAnggaran) {
            session()->flash('error', 'Tidak ada data anggaran yang ditemukan.');
            return redirect()->back();
        }

        // Ambil tanggal mulai periode dari data SetAnggaran
        $tglSetAnggaran = $setAnggaran->tgl_mulai_periode;
        if (!$tglSetAnggaran) {
            session()->flash('error', 'Tanggal mulai periode tidak ditemukan pada data anggaran.');
            return redirect()->back();
        }

        $periode = $setAnggaran->jenis_periode; // 'bulan' atau 'tahun'
        $total_periode = $setAnggaran->total_periode;

        // Menggunakan Carbon untuk mengatur tanggal akhir periode
        $endDate = $periode == 'bulan' 
            ? Carbon::parse($tglSetAnggaran)->addMonths($total_periode)
            : Carbon::parse($tglSetAnggaran)->addYears($total_periode);

        // Tanggal dan waktu sekarang
        $currentDate = Carbon::now();

        // Memastikan kita berada dalam rentang periode yang sesuai (>= tanggal mulai dan <= tanggal akhir)
        if ($currentDate->lt(Carbon::parse($tglSetAnggaran)) || $currentDate->gt($endDate)) {
            session()->flash('error', 'Tidak ada data proker yang berlaku untuk periode ini.');
            return redirect()->back();
        }

        $file = $request->file('file_lpj');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('lpj');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Cek apakah data LPJ untuk id_proker ini sudah ada
        $lpj = LPJ::where('id_proker', $request->id_proker)->first();

        if ($lpj) {
            // Jika ada, update file dan data lainnya
            if (File::exists($directory . '/' . $lpj->file_lpj)) {
                File::delete($directory . '/' . $lpj->file_lpj);
            }
            $lpj->file_lpj = $filename;
            $lpj->dana_disetujui = $validatedData['dana_disetujui'];
            $lpj->status = 'Pending';
            $lpj->catatan = 'Belum ada catatan';
        } else {
            // Jika tidak ada, buat data baru
            $lpj = new LPJ();
            $lpj->file_lpj = $filename;
            $lpj->dana_disetujui = $validatedData['dana_disetujui'];
            $lpj->status = 'Pending';
            $lpj->catatan = 'Belum ada catatan';
            $lpj->id_proker = $request->id_proker;
            $lpj->status_flow_lpj = 0;
        }

        // Pindahkan file ke direktori yang ditentukan
        $file->move($directory, $filename);

        $proker = Proker::where('id', $request->id_proker)->first();
        if (!$proker) {
            return redirect()->back()->with('error', 'Proker not found');
        }
    
        if (empty($proker->ttd_ketupel)) {
            return redirect()->back()->with('error', 'TTD Ketupel tidak lengkap');
        }
    
        $namaOrganisasi = $proker->organisasi->nama_organisasi;
        
        $proker = Proker::where('id', $request->id_proker)->first();
        if (!$proker) {
            return redirect()->back()->with('error', 'Proker not found');
        }
    
        if (empty($proker->ttd_ketupel)) {
            return redirect()->back()->with('error', 'TTD Ketupel tidak lengkap');
        }

        $codeJabatan = 5;
        $status_flow = 0;
        $namaOrganisasi = $proker->organisasi->nama_organisasi;

        if ($codeJabatan !== null) {
            $user = User::join('jabatan', 'users.jabatan_id', '=', 'jabatan.jabatan_id')
                ->where('jabatan.code_jabatan', $codeJabatan)
                ->when($status_flow == 2, function($query) use ($namaOrganisasi) {
                    return $query->whereRaw('LOWER(users.organization) = ?', [strtolower($namaOrganisasi)]);
                })
                ->when($status_flow == 3, function($query) {
                    return $query->whereRaw('LOWER(users.organization) LIKE ?', ['%bem%']);
                })
                ->when($status_flow == 4, function($query) {
                    return $query->whereRaw('LOWER(users.organization) LIKE ?', ['%bpm%']);
                })
                ->select('users.email', 'users.name')
                ->first();

                $sendEmail = $this->sendNotificationEmail($user);

                if($sendEmail){
                   // Simpan perubahan atau data baru
                    $lpj->save();
                    return redirect()->back()->with('success', 'File LPJ berhasil diupload!');
                }else{
                    return redirect()->back()->with('error', 'gagal kirim email!');
                }
        }

        return redirect()->back()->with('error', 'File LPJ Gagal  diupload!');
    }


    public function update(Request $request, $id_proker)
    {
        // Validasi input
        $validatedData = $request->validate([
            'file_lpj' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'dana_disetujui' => 'required',
        ]);

        // Temukan LPJ berdasarkan id_proker
        $lpj = LPJ::where('id_proker', $id_proker)->first();

        if (!$lpj) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Ambil data SetAnggaran terbaru
        $setAnggaran = SetAnggaran::orderBy('updated_at', 'desc')->first();
        if (!$setAnggaran) {
            session()->flash('error', 'Tidak ada data anggaran yang ditemukan.');
            return redirect()->back();
        }

        // Ambil tanggal mulai periode dari data SetAnggaran
        $tglSetAnggaran = $setAnggaran->tgl_mulai_periode;
        if (!$tglSetAnggaran) {
            session()->flash('error', 'Tanggal mulai periode tidak ditemukan pada data anggaran.');
            return redirect()->back();
        }

        $periode = $setAnggaran->jenis_periode; // 'bulan' atau 'tahun'
        $total_periode = $setAnggaran->total_periode;

        // Menggunakan Carbon untuk mengatur tanggal akhir periode
        $endDate = $periode == 'bulan' 
            ? Carbon::parse($tglSetAnggaran)->addMonths($total_periode)
            : Carbon::parse($tglSetAnggaran)->addYears($total_periode);

        // Tanggal dan waktu sekarang
        $currentDate = Carbon::now();

        // Memastikan kita berada dalam rentang periode yang sesuai (>= tanggal mulai dan <= tanggal akhir)
        if ($currentDate->lt(Carbon::parse($tglSetAnggaran)) || $currentDate->gt($endDate)) {
            session()->flash('error', 'Tidak ada data proker yang berlaku untuk periode ini.');
            return redirect()->back();
        }

        // Jika ada file yang diunggah
        if ($request->hasFile('file_lpj')) {
            $file = $request->file('file_lpj');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $directory = public_path('lpj');

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Pindahkan file baru ke direktori
            $file->move($directory, $filename);

            // Hapus file lama jika ada
            if ($lpj->file_lpj && File::exists($directory . '/' . $lpj->file_lpj)) {
                File::delete($directory . '/' . $lpj->file_lpj);
            }

            // Perbarui kolom file_lpj dengan nama file baru
            $lpj->file_lpj = $filename;
        }

        // Perbarui kolom dana_disetujui
        $lpj->dana_disetujui = $validatedData['dana_disetujui'];

        // Simpan perubahan
        $lpj->save();

        return redirect()->back()->with('success', 'File LPJ berhasil diperbarui!');
    }

    public function approvedLpj($lpjId)
    {
        $currentUser = $this->getCurrentUser();
        $jabatanId = $currentUser['code_jabatan'];
        $jabatan = $currentUser['jabatan'];
        $organisasi = $currentUser['organisasi'];
    
        $lpjFolderPath = public_path('lpj');
        if (!File::exists($lpjFolderPath)) {
            File::makeDirectory($lpjFolderPath, 0755, true);
        }
    
        $lpj = LPJ::with('proker.organisasi')->find($lpjId);
        if (!$lpj) {
            Session::flash('error', 'LPJ not found.');
            return redirect()->back();
        }
    
        $filePath = public_path('lpj/' . $lpj->file_lpj);
        if (!File::exists($filePath)) {
            Session::flash('error', 'LPJ file not found.');
            return redirect()->back();
        }

        $mappingCheckLpj = new MappingCheckLpj();
        $signatures = $mappingCheckLpj->updateStatusFlowLpj($lpjId, $jabatanId, $organisasi, $jabatan);
    
        $proker = Proker::where('id', $lpj->id_proker)->first();
        if (!$proker) {
            return redirect()->back()->with('error', 'Proker not found');
        }
    
        if (empty($proker->ttd_ketupel)) {
            return redirect()->back()->with('error', 'TTD Ketupel tidak lengkap');
        }
    
        $namaOrganisasi = $proker->organisasi->nama_organisasi;
        
        $user = $this->processStatusFlow($lpjId, $jabatanId, $organisasi, $jabatan, $namaOrganisasi);
    
        if ($user) {
            $result = $this->sendNotificationEmail($user);
    
            if ($result) {
                Session::flash('success', 'Email has been sent.');
            } else {
                Session::flash('error', 'Failed to sent the email.');
                return redirect()->back();
            }
        }
    
        $ketupel = [
            'name' => $proker->nama_ketupel,
            'nim' => $proker->nim_ketupel,
            'ttd' => public_path('ttd') . '/' . $proker->ttd_ketupel
        ];
    
        $namaKegiatan = $proker->nama_proker;
    
        if ($proker->organisasi->nama_organisasi == 'BEM') {
            $html = view('pdf.signatures', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        } elseif (stripos($proker->organisasi->nama_organisasi, 'UKM') !== false) {
            $html = view('pdf.ukm-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        } else {
            $html = view('pdf.hima-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        }
    
        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');
    
        $path = public_path('lpj');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    
        $oldFilePath = public_path('lpj/' . $lpj->pengesahan);
        if (File::exists($oldFilePath)) {
            File::delete($oldFilePath);
        }
    
        $fileName = Str::uuid() . '.pdf';
        $newFilePath = $path . '/' . $fileName;
    
        $pdf->save($newFilePath);
        $lpj->pengesahan = $fileName;
        $save = $lpj->save();
    
        if ($signatures != false && $save) {
            Session::flash('success', 'LPJ has been successfully approved.');
        } else {
            Session::flash('error', 'Failed to approve the LPJ.');
        }
    
        return redirect()->back();
    }
    

    private function processStatusFlow($lpjId, $jabatanId, $organisasi, $jabatan, $namaOrganisasi)
    {
        $mappingCheckLpj = new MappingCheckLpj();
        $signatures = $mappingCheckLpj->updateStatusFlowLpj($lpjId, $jabatanId, $organisasi, $jabatan);
        $status_flow = $signatures['status_flow'] == 0 ? $signatures['status_flow'] + 2 : $signatures['status_flow'] + 1;
        if ($signatures !== false) {
            $signatures = $this->filterTtdList($signatures['ttdList'], $jabatanId, $organisasi);
        }

        $status_code_mapping = [
            0 => 6, // SEKRETARIS
            1 => 6, // REVISI
            2 => stripos($namaOrganisasi, 'UKM') !== false ? 5 : 5, // KETUA UKM atau KETUA HIMA
            3 => 5, // KETUA BEM
            4 => 5, // KETUA BPM
            5 => 4, // PEMBINA
            6 => 8, // KETUA PRODI
            7 => 3, // KETUA JURUSAN
            8 => 2, // KOORDINATOR SUB BAGIAN
            9 => 1  // WAKIL DIREKTUR
        ];

        $codeJabatan = $status_code_mapping[$status_flow] ?? null;

        if ($codeJabatan !== null) {
            $user = User::join('jabatan', 'users.jabatan_id', '=', 'jabatan.jabatan_id')
                ->where('jabatan.code_jabatan', $codeJabatan)
                ->when($status_flow == 2, function($query) use ($namaOrganisasi) {
                    return $query->whereRaw('LOWER(users.organization) = ?', [strtolower($namaOrganisasi)]);
                })
                ->when($status_flow == 3, function($query) {
                    return $query->whereRaw('LOWER(users.organization) LIKE ?', ['%bem%']);
                })
                ->when($status_flow == 4, function($query) {
                    return $query->whereRaw('LOWER(users.organization) LIKE ?', ['%bpm%']);
                })
                ->select('users.email', 'users.name')
                ->first();

            return $user;
        }
        
        return null;
    }

    private function sendNotificationEmail($user)
    {
        if ($user) {
            $emailTarget = $user->email;
            $nameTarget = $user->name;

            $details = [
                'receiver_name' => $nameTarget,
                'proposal_title' => 'Pemberitahuan LPJ Pengajuan Masuk',
                'sender_name' => 'Tim IT',
                'date' => now()->format('Y-m-d')
            ];

            $recipientEmail = $emailTarget;

            return $this->sendEmail($details, $recipientEmail);
        }

        return false;
    }


    private function filterTtdList($ttdList, $jabatanId, $organisasi)
    {
        foreach ($ttdList as &$ttd) {
            $isMatch = false;
    
            if ($jabatanId == 5) {
                if (stripos($organisasi, 'HIMA') !== false) {
                    $isMatch = stripos($ttd['organisasi'], 'HIMA') !== false && $ttd['code_jabatan'] == 5;
                } elseif (stripos($organisasi, 'UKM') !== false) {
                    $isMatch = stripos($ttd['organisasi'], 'UKM') !== false && $ttd['code_jabatan'] == 5;
                } elseif ($organisasi == 'BEM') {
                    $isMatch = ($ttd['organisasi'] == 'BEM' || stripos($ttd['organisasi'], 'HIMA') !== false || stripos($ttd['organisasi'], 'UKM') !== false) && $ttd['code_jabatan'] == 5;
                }elseif ($organisasi == 'BPM') {
                    $isMatch = ($ttd['organisasi'] == 'BPM' || $ttd['organisasi'] == 'BEM' || stripos($ttd['organisasi'], 'HIMA') !== false || stripos($ttd['organisasi'], 'UKM') !== false) && $ttd['code_jabatan'] == 5;
                }
            } else if ($jabatanId == 4) {
                $isMatch = $ttd['code_jabatan'] == 4 || $ttd['code_jabatan'] == 5;
            } else if ($jabatanId == 8) {
                $isMatch = $ttd['code_jabatan'] == 8 || $ttd['code_jabatan'] == 4 || $ttd['code_jabatan'] == 5;
            } else if ($jabatanId == 3) {
                $isMatch = $ttd['code_jabatan'] == 3 || $ttd['code_jabatan'] == 8 || $ttd['code_jabatan'] == 4 || $ttd['code_jabatan'] == 5;
            } else if ($jabatanId == 2) {
                $isMatch = ($ttd['code_jabatan'] == 2 || $ttd['code_jabatan'] == 3 || $ttd['code_jabatan'] == 8 || $ttd['code_jabatan'] == 4 || $ttd['code_jabatan'] == 5) && $ttd['role'] != 1;
            } else if ($jabatanId == 1) {
                $isMatch = $ttd['code_jabatan'] == 2 || $ttd['code_jabatan'] == 2 || $ttd['code_jabatan'] == 3 || $ttd['code_jabatan'] == 8 || $ttd['code_jabatan'] == 4 || $ttd['code_jabatan'] == 5;
            }
            // Jika tidak cocok, setel semua atribut ke null
            if (!$isMatch) {
                $ttd = array_fill_keys(array_keys($ttd), null);
            }
        }
    
        return $ttdList;
    }

    public function updateRevisiLpj(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $jabatanId = $currentUser['code_jabatan'];
        $jabatan = $currentUser['jabatan'];
        $organisasi = $currentUser['organisasi'];
        $lpjId = $request->input('lpj_id');
        $catatan = $request->input('catatan');
        $danadisetujui = $request->input('dana_disetujui');

        $mappingCheckLpj = new MappingCheckLpj();

        if ($mappingCheckLpj->updateRevisiLpj($lpjId, $jabatanId, $organisasi, $jabatan, $catatan)) {
            Session::flash('success', 'LPJ has been successfully revised.');
        } else {
            Session::flash('error', 'Failed to revise the LPJ.');
        }

        return redirect()->back();
    }

    public function createSignaturePdf(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $lpjId = $request->input('lpj_id');
        $jabatanId = $currentUser['jabatan_id'];
        $jabatan = $currentUser['jabatan'];

        $lpj = LPJ::find($lpjId);
        if (!$lpj) {
            return redirect()->back()->with('error', 'LPJ not found');
        }

        $proker = Proker::where('id', $lpj->id_proker)->first();
        if (!$proker) {
            return redirect()->back()->with('error', 'Proker not found');
        }

        if (empty($proker->ttd_ketupel)) {
            return redirect()->back()->with('error', 'TTD Ketupel tidak lengkap');
        }

        $namaKegiatan = $proker->nama_proker;
        $organisasi = $proker->organisasi->nama_organisasi;

        $mappingCheckLpj = new MappingCheckLpj();
        $signatures = $mappingCheckLpj->signatureCreateLpj($jabatanId, $lpjId, $jabatan);

        $ketupel = [
            'name' => $proker->nama_ketupel,
            'nim' => $proker->nim_ketupel,
            'ttd' => public_path('ttd') . '/' . $proker->ttd_ketupel
        ];

        // Pilih template berdasarkan organisasi
        if ($organisasi == 'BEM') {
            $html = view('pdf.signatures', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        } elseif (stripos($organisasi, 'UKM') !== false) {
            $html = view('pdf.ukm-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        } else {
            $html = view('pdf.hima-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        }

        // Membuat PDF
        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');
        $pdfData = $pdf->output();

        $path = public_path('lpj');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        // Hapus file pengesahan lama jika ada
        $oldFilePath = public_path('lpj/' . $lpj->pengesahan);
        if (File::exists($oldFilePath)) {
            File::delete($oldFilePath);
        }

        // Simpan PDF baru dengan nama file unik
        $fileName = Str::uuid() . '.pdf';
        $newFilePath = $path . '/' . $fileName;
        $pdf->save($newFilePath);

        // Perbarui path pengesahan di database
        $lpj->pengesahan = $fileName;
        $lpj->save();

        // Mengambil pengguna yang sesuai untuk notifikasi
        $user = $this->getUserForNotification($proker);

        // Detail untuk email
        $details = [
            'receiver_name' => $user->name,
            'proposal_title' => 'Proposal Approval',
            'sender_name' => 'Tim IT',
            'file_type' => 'Proposal Document/pdf',
            'file_title' => 'Pemberitahuan Proposal Pengajuan Masuk',
            'approval_date' => now()->format('Y-m-d'),
        ];
        

        // Memanggil fungsi sendPdfEmail dengan parameter yang benar
        $sendEmailSuccess = $this->sendPdfEmail($user->email, $pdfData, $details);

        if (!$sendEmailSuccess) {
            return redirect()->back()->with('error', 'Gagal mengirim email!');
        }

        return $pdf->stream('document.pdf');
    }

    private function getUserForNotification($proker)
    {
        $codeJabatan = 6;
        $namaOrganisasi = $proker->organisasi->nama_organisasi;

        return User::join('jabatan', 'users.jabatan_id', '=', 'jabatan.jabatan_id')
            ->where('jabatan.code_jabatan', $codeJabatan)
            ->whereRaw('LOWER(users.organization) = ?', [strtolower($namaOrganisasi)])
            ->select('users.email', 'users.name')
            ->first();
    }

}
