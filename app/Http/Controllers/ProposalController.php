<?php

namespace App\Http\Controllers;

use App\Models\Proker;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Mail\ProposalApproved;
use DB;
use Session;
use App\Models\MappingCheck;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SetAnggaran;
use Carbon\Carbon;
use App\Mail\NotificationEmail;


class ProposalController extends Controller
{
    public function index()
    {
        // Mendapatkan informasi pengguna saat ini
        $currentUser = $this->getCurrentUser();
        $jabatan = $currentUser['jabatan'];
        $organisasiUser = $currentUser['organisasi'];
    
        // Ambil data SetAnggaran terbaru
        $setAnggaran = SetAnggaran::orderBy('updated_at', 'desc')->first();
        if (!$setAnggaran) {
            session()->flash('error', 'Tidak ada data anggaran yang ditemukan.');
            return view('upload-proposal', [
                'listproker' => collect([]), // Koleksi kosong jika tidak ada data
                'jabatan' => $jabatan,
                'orguser' => $organisasiUser
            ]);
        }
    
        // Ambil tanggal mulai periode dari data SetAnggaran
        $tglSetAnggaran = $setAnggaran->tgl_mulai_periode;
        if (!$tglSetAnggaran) {
            session()->flash('error', 'Tanggal mulai periode tidak ditemukan pada data anggaran.');
            return view('upload-proposal', [
                'listproker' => collect([]), // Koleksi kosong jika tidak ada data
                'jabatan' => $jabatan,
                'orguser' => $organisasiUser
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
            return view('upload-proposal', [
                'listproker' => collect([]), // Koleksi kosong jika tidak ada data valid dalam rentang periode
                'jabatan' => $jabatan,
                'orguser' => $organisasiUser
            ]);
        }
    
        // Mengambil data proker dengan organisasi dan proposal terkait yang berada dalam rentang periode aktif
        $proker = Proker::with(['organisasi', 'proposal'])
            ->whereBetween('created_at', [$tglSetAnggaran, $endDate])
            ->get();
    
        // Mengirim data pengguna ke view 'upload-proposal'
        return view('upload-proposal', [
            'listproker' => $proker,
            'jabatan' => $jabatan,
            'orguser' => $organisasiUser
        ]);
    }
    


    public function indexproposal()
    {
        // Mengirim data pengguna ke view 'lihat-proposal'
        return view('lihat-proposal');
    }

    public function pengecekanproposal()
    {
        $currentUser = $this->getCurrentUser();
        $organisasiUser = $currentUser['organisasi'];
        $codeJabatan = $currentUser['code_jabatan'];
    
        // Ambil data SetAnggaran terbaru
        $setAnggaran = SetAnggaran::orderBy('updated_at', 'desc')->first();
        if (!$setAnggaran) {
            session()->flash('error', 'Tidak ada data anggaran yang ditemukan.');
            return view('pengecekan-proposal', [
                'listproker' => collect([]), // Koleksi kosong jika tidak ada data
                'orguser' => $organisasiUser,
                'codeJabatan' => $codeJabatan
            ]);
        }
    
        // Ambil tanggal mulai periode dari data SetAnggaran
        $tglSetAnggaran = $setAnggaran->tgl_mulai_periode;
        if (!$tglSetAnggaran) {
            session()->flash('error', 'Tanggal mulai periode tidak ditemukan pada data anggaran.');
            return view('pengecekan-proposal', [
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
            return view('pengecekan-proposal', [
                'listproker' => collect([]), // Koleksi kosong jika tidak ada data valid dalam rentang periode
                'orguser' => $organisasiUser,
                'codeJabatan' => $codeJabatan
            ]);
        }
    
        // Mengambil data proker dengan organisasi dan proposal terkait yang berada dalam rentang periode aktif
        $proker = Proker::with(['organisasi', 'proposal'])
            ->whereBetween('created_at', [$tglSetAnggaran, $endDate])
            ->get();
    
        // Mengirim data pengguna ke view 'pengecekan-proposal'
        return view('pengecekan-proposal', [
            'listproker' => $proker,
            'orguser' => $organisasiUser,
            'codeJabatan' => $codeJabatan
        ]);
    }
    

    public function pengecekanproposalbpm()
    {
        // Mengirim data pengguna ke view 'pengecekanproposal-bpm'
        return view('pengecekanproposal-bpm');
    }

    public function approvedProposal($proposalId)
    {

        $currentUser = $this->getCurrentUser();
        $jabatanId = $currentUser['code_jabatan'];
        $jabatan = $currentUser['jabatan'];
        $organisasi = $currentUser['organisasi'];
    
        $proposal = Proposal::find($proposalId);
        if (!$proposal) {
            Session::flash('error', 'Proposal not found.');
            return redirect()->back();
        }
    
        $filePath = public_path('files/' . $proposal->file_proposal);
        if (!File::exists($filePath)) {
            Session::flash('error', 'Proposal file not found.');
            return redirect()->back();
        }
    
        $proker = Proker::where('id', $proposal->id_proker)->first();
        if (!$proker) {
            return redirect()->back()->with('error', 'Proker not found');
        }
    
        if (empty($proker->ttd_ketupel)) {
            return redirect()->back()->with('error', 'TTD Ketupel tidak lengkap');
        }

        $mappingCheck = new MappingCheck();
        $signatures = $mappingCheck->updateStatusFlow($proposalId, $jabatanId, $organisasi, $jabatan);
    
        if ($signatures !== false) {
            $status_flow = $signatures['status_flow'] == 0 ? $signatures['status_flow'] + 2 : $signatures['status_flow'] + 1;
            $signatures = $this->filterTtdList($signatures['ttdList'], $jabatanId, $organisasi);

            $ruteBem = [5,5,4,2,1];
            $ruteHima = [5,5,5,4,8,3,2,1];
            $ruteUkm = [5,5,5,4,2,1];

            $namaOrganisasi = $proker->organisasi->nama_organisasi;
            
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
        
                if ($user) {
                    $emailTarget = $user->email;
                    $nameTarget = $user->name;

                    //penggunaan sistem Email
                    $details = [
                        'receiver_name' => $nameTarget,
                        'proposal_title' => 'Pemberitahuan Proposal Pengajuan Masuk',
                        'sender_name' => 'Tim IT',
                        'date' => now()->format('Y-m-d')
                    ];
                            
                    $recipientEmail = $emailTarget;
                    
                    $result = $this->sendEmail($details, $recipientEmail);
                    
                    if ($result) {
                        Session::flash('success', 'Email has been sent.');
                    } else {
                        Session::flash('error', 'Failed to sent the email.');
                        return redirect()->back();
                    }
                    
                }
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
    
        $path = public_path('pengesahan');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    
        // Cek apakah sudah ada file pengesahan sebelumnya, jika ada maka hapus
        $oldFilePath = public_path('pengesahan/' . $proposal->pengesahan);
        if (File::exists($oldFilePath)) {
            File::delete($oldFilePath);
        }
    
        $fileName = Str::uuid() . '.pdf';
        $newFilePath = $path . '/' . $fileName;
    
        $pdf->save($newFilePath);
        $proposal->pengesahan = $fileName;
        $save = $proposal->save();
    
        if ($signatures != false && $save) {
            Session::flash('success', 'Proposal has been successfully approved.');
        } else {
            Session::flash('error', 'Failed to approve the proposal.');
        }
    
        return redirect()->back();
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

    public function updateRevisi(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $jabatanId = $currentUser['code_jabatan'];
        $jabatan = $currentUser['jabatan'];
        $organisasi = $currentUser['organisasi'];
        $proposalId = $request->input('proposal_id');
        $catatan = $request->input('catatan');

        // Create a new instance of MappingCheck
        $mappingCheck = new MappingCheck();

        // Attempt to update the status flow
        if ($mappingCheck->updateRevisi($proposalId, $jabatanId, $organisasi, $jabatan, $catatan)) {
            Session::flash('success', 'Proposal has been successfully approved.');
        } else {
            Session::flash('error', 'Failed to approve the proposal.');
        }

        return redirect()->back();
    }

    public function createSignaturePdf(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $proposalId = $request->input('proposal_id');
        $jabatanId = $currentUser['jabatan_id'];
        $jabatan = $currentUser['jabatan'];
        $namaKegiatan = $request->input('proker');
        $organisasi = $request->input('organisasi');

        $proposal = Proposal::find($proposalId);
        if (!$proposal) {
            return redirect()->back()->with('error', 'Proposal tidak ditemukan');
        }

        $proker = Proker::where('id', $proposal->id_proker)->first();
        if (!$proker) {
            return redirect()->back()->with('error', 'Proker tidak ditemukan');
        }

        if (empty($proker->ttd_ketupel)) {
            return redirect()->back()->with('error', 'TTD Ketupel tidak lengkap');
        }

        $model = new MappingCheck();
        $signatures = $model->signatureCreate($jabatanId, $proposalId, $jabatan);

        $ketupel = [
            'name' => $proker->nama_ketupel,
            'nim' => $proker->nim_ketupel,
            'ttd' => public_path('ttd') . '/' . $proker->ttd_ketupel
        ];

        // Pilih template PDF berdasarkan organisasi
        if ($organisasi == 'BEM') {
            $html = view('pdf.signatures', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        } elseif (stripos($organisasi, 'UKM') !== false) {
            $html = view('pdf.ukm-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        } else {
            $html = view('pdf.hima-signature', compact('signatures', 'namaKegiatan', 'ketupel'))->render();
        }

        // Membuat PDF di memori
        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');
        $pdfData = $pdf->output(); // Mendapatkan data PDF dalam bentuk biner

        // Mengambil pengguna yang sesuai untuk notifikasi
        $user = $this->getUserForNotification($proker);

        // Kirim notifikasi email dengan lampiran file PDF
        $details = [
            'receiver_name' => $user->name,
            'proposal_title' => 'Pemberitahuan Proposal Pengajuan Masuk',
            'sender_name' => 'Tim IT',
            'date' => now()->format('Y-m-d'),
        ];

        $sendEmailSuccess = $this->sendPdfEmail($user->email, $pdfData, 'proposal_approval.pdf', $details);

        if (!$sendEmailSuccess) {
            // Jika email gagal dikirim, set flash message dan redirect kembali
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
