<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use App\Models\LPJ;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SetAnggaran;
use Carbon\Carbon;

class AnggaranController extends Controller
{
    public function index()
    {
        $anggaran = Anggaran::with('organisasi')->get();
        $organisasis = Organisasi::All();
        $totalAnggaran = SetAnggaran::All();

        return view('anggaran-bpm', ['anggaran' => $anggaran, 'organisasis' => $organisasis, 'totalAnggaran' => $totalAnggaran]);
    }

    public function indexanggaranorganisasi()
    {
        $currentUser = $this->getCurrentUser();
        $jabatanId = $currentUser['jabatan_id'];
        $org = $currentUser['organisasi'];
        $TA = SetAnggaran::all();
        
        // Dapatkan data SetAnggaran terbaru
        $setAnggaran = SetAnggaran::orderBy('updated_at', 'desc')->first();
        if (!$setAnggaran) {
            Session::flash('error', 'Tidak ada data anggaran yang ditemukan.');
            return view('anggaran-organisasi', [
                'anggaran' => collect([]), // Koleksi kosong jika tidak ada data
                'totalAnggaran' => $TA,
            ]);
        }

        // Ambil tanggal mulai periode dari data SetAnggaran
        $tglSetAnggaran = $setAnggaran->tgl_mulai_periode;
        if (!$tglSetAnggaran) {
            Session::flash('error', 'Tanggal mulai periode tidak ditemukan pada data anggaran.');
            return view('anggaran-organisasi', [
                'anggaran' => collect([]), // Koleksi kosong jika tidak ada data
                'totalAnggaran' => $TA,
            ]);
        }

        $periode = $setAnggaran->jenis_periode; // 'bulan' atau 'tahun'
        $total_periode = $setAnggaran->total_periode;
        $totalAnggaran = $setAnggaran->total_anggaran; // Dapatkan total_anggaran

        // Menggunakan Carbon untuk mengatur tanggal akhir periode
        $endDate = $periode == 'bulan' 
            ? Carbon::parse($tglSetAnggaran)->addMonths($total_periode) 
            : Carbon::parse($tglSetAnggaran)->addYears($total_periode);

        // Tanggal dan waktu sekarang
        $currentDate = Carbon::now();

        // Memastikan kita berada dalam rentang periode yang sesuai (>= tanggal mulai dan <= tanggal akhir)
        if ($currentDate->lt(Carbon::parse($tglSetAnggaran)) || $currentDate->gt($endDate)) {
            Session::flash('error', 'Tidak ada data anggaran yang berlaku untuk periode ini.');
            return view('anggaran-organisasi', [
                'anggaran' => collect([]), // Koleksi kosong jika tidak ada data valid dalam rentang periode
                'totalAnggaran' => $TA,
            ]);
        }

        // Query data LPJ yang hanya berada dalam rentang waktu yang berjalan
        $query = LPJ::with(['proker.organisasi'])
            ->whereNotNull('file_lpj')
            ->whereNotNull('dana_disetujui')
            ->whereHas('proker', function($q) use ($tglSetAnggaran, $endDate) {
                $q->whereBetween('created_at', [$tglSetAnggaran, $endDate]);
            }); // Filter by created_at

        $lpjData = $query->get();

        // Variabel untuk menyimpan total sisa anggaran
        $totalSisaAnggaran = $totalAnggaran;

        // Memproses data untuk tampilan
        $data = $lpjData->map(function($lpj) use (&$totalSisaAnggaran) {
            $totalAnggaranOrganisasi = $lpj->proker->organisasi->anggarans->sum('total_anggaran');
            $sisaAnggaran = $totalAnggaranOrganisasi - $lpj->dana_disetujui;

            // Mengurangi total sisa anggaran dengan dana disetujui
            $totalSisaAnggaran -= $lpj->dana_disetujui;

            return [
                'id' => $lpj->id,
                'nama_organisasi' => $lpj->proker->organisasi->nama_organisasi,
                'nama_proker' => $lpj->proker->nama_proker,
                'dana_diajukan' => $lpj->proker->dana_diajukan,
                'dana_disetujui' => $lpj->dana_disetujui,
                'sisa_anggaran' => $sisaAnggaran, // Sisa anggaran untuk organisasi tersebut
                'total_sisa_anggaran' => $totalSisaAnggaran, // Total sisa anggaran setelah pengurangan bertahap
            ];
        });

        // Filter data berdasarkan organisasi jika bukan admin
        if ($jabatanId != 1) { // Asumsikan jabatan ID 1 adalah admin
            $dataFiltered = $data->filter(function($item) use ($org) {
                return $item['nama_organisasi'] == $org;
            });
        } else {
            $dataFiltered = $data;
        }

        return view('anggaran-organisasi', [
            'anggaran' => $dataFiltered,
            'totalAnggaran' => $TA,
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input jika diperlukan
        $validatedData = $request->validate([
            'id_organisasi' => 'required|exists:tb_organisasi,id',
            'jumlah_mhs' => 'required|numeric',
            'jumlah_anggaran' => 'required|numeric',
            'total_anggaran' => 'required|numeric',
        ]);

        // Mendapatkan data organisasi
        $organisasi = Organisasi::find($request->id_organisasi);

        // Mendapatkan data anggaran yang sudah ada
        $totalAnggaranAll = SetAnggaran::all();
        if ($totalAnggaranAll->isEmpty()) {
            return redirect()->back()->with('error', 'Anggaran belum diatur.');
        }

        $setAnggaran = $totalAnggaranAll->first();
        $tglSetAnggaran = $setAnggaran->tgl_mulai_periode;
        if (!$tglSetAnggaran) {
            return redirect()->back()->with('error', 'Tanggal mulai periode tidak ditemukan pada data anggaran.');
        }

        $periode = $setAnggaran->jenis_periode; // bulan atau tahun
        $total_periode = $setAnggaran->total_periode;
        $jumlah_anggaran = $setAnggaran->total_anggaran;

        // Menggunakan Carbon untuk mengatur tanggal akhir periode
        $endDate = $periode == 'bulan' 
            ? Carbon::parse($tglSetAnggaran)->addMonths($total_periode)
            : Carbon::parse($tglSetAnggaran)->addYears($total_periode);

        // Pastikan tanggal dan waktu sekarang berada dalam rentang periode anggaran
        $currentDate = Carbon::now();
        if ($currentDate->lt($tglSetAnggaran) || $currentDate->gt($endDate)) {
            return redirect()->back()->with('error', 'Anggaran yang disimpan tidak masuk dalam periode yang sesuai.');
        }

        // Mendapatkan total anggaran yang telah digunakan dalam periode tersebut
        $totalUsedAnggaran = Anggaran::where('created_at', '>=', $tglSetAnggaran)
                                    ->where('created_at', '<=', $endDate)
                                    ->sum('total_anggaran');

        // Pastikan jumlah anggaran yang diinput tidak melebihi anggaran total yang tersedia
        if ($totalUsedAnggaran + $request->jumlah_anggaran > $jumlah_anggaran) {
            return redirect()->back()->with('error', 'Jumlah anggaran yang dimasukkan melebihi total anggaran yang tersedia.');
        }

        // Simpan data ke dalam database
        $anggaran = new Anggaran();
        $anggaran->id_organisasi = $request->id_organisasi;
        $anggaran->jumlah_mhs = $request->jumlah_mhs;
        $anggaran->jumlah_anggaran = $request->jumlah_anggaran;
        $anggaran->total_anggaran = $request->total_anggaran;
        $anggaran->save();

        // Berhasil, kirim respon
        return redirect('/anggaran')->with('success', 'Data Anggaran berhasil Disimpan!');
    }


    

    public function update(Request $request, $id)
    {
    // Validasi input jika diperlukan
    $validatedData = $request->validate([
        'id_organisasi' => 'required|exists:tb_organisasi,id',
        'jumlah_mhs' => 'required|numeric',
        'jumlah_anggaran' => 'required|numeric',
        'total_anggaran' => 'required|numeric',
    ]);

    // Cari data anggaran berdasarkan id
    $anggaran = Anggaran::find($id);

    if (!$anggaran) {
        // Jika data tidak ditemukan, kirim respon error
        return redirect('/anggaran')->with('error', 'Data Anggaran tidak ditemukan!');
    }

    // Update data di dalam database
    $anggaran->id_organisasi = $request->id_organisasi;
    $anggaran->jumlah_mhs = $request->jumlah_mhs;
    $anggaran->jumlah_anggaran = $request->jumlah_anggaran;
    $anggaran->total_anggaran = $request->total_anggaran;
    $anggaran->save();

    // Berhasil, kirim respon
    return redirect('/anggaran')->with('success', 'Data Anggaran berhasil diupdate!');
    }

    public function delete($id)
    {
        try {
            // Cari data anggaran berdasarkan id
            $anggaran = Anggaran::findOrFail($id);

            // Hapus data anggaran
            $anggaran->delete();

            // Redirect dengan pesan sukses
            return redirect('/anggaran')->with('success', 'Data anggaran berhasil dihapus!');
        } catch (\Exception $e) {
            // Redirect dengan pesan error jika terjadi kesalahan
            return redirect('/anggaran')->with('error', 'Terjadi kesalahan saat menghapus data anggaran');
        }
    }

    public function cetakLaporan(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_organisasi' => 'required|string'
        ]);

        // Ambil nilai dari form
        $namaOrganisasi = $request->input('nama_organisasi');

        // Dapatkan data SetAnggaran terbaru
        $setAnggaran = SetAnggaran::orderBy('updated_at', 'desc')->first();
        if (!$setAnggaran) {
            return redirect()->back()->with('error', 'Tidak ada data anggaran yang ditemukan.');
        }

        // Ambil tanggal mulai periode dari data SetAnggaran
        $tglSetAnggaran = $setAnggaran->tgl_mulai_periode;
        if (!$tglSetAnggaran) {
            return redirect()->back()->with('error', 'Tanggal mulai periode tidak ditemukan pada data anggaran.');
        }

        $periode = $setAnggaran->jenis_periode; // 'bulan' atau 'tahun'
        $total_periode = $setAnggaran->total_periode;
        $totalAnggaran = $setAnggaran->total_anggaran; // Dapatkan total_anggaran

        // Menggunakan Carbon untuk mengatur tanggal dan waktu akhir periode
        $endDate = $periode == 'bulan' 
            ? Carbon::parse($tglSetAnggaran)->addMonths($total_periode) 
            : Carbon::parse($tglSetAnggaran)->addYears($total_periode);

        // Tanggal dan waktu sekarang
        $currentDate = Carbon::now();

        // Memastikan kita berada dalam rentang periode yang sesuai (>= tanggal mulai dan <= tanggal akhir)
        if ($currentDate->lt($tglSetAnggaran) || $currentDate->gt($endDate)) {
            return redirect()->back()->with('error', 'Tidak ada data anggaran yang berlaku untuk periode ini.');
        }

        // Query data LPJ yang hanya berada dalam rentang waktu yang berjalan
        $query = LPJ::with(['proker.organisasi'])
                    ->whereNotNull('file_lpj')
                    ->whereNotNull('dana_disetujui')
                    ->whereBetween('created_at', [$tglSetAnggaran, $endDate]);

        $lpjData = $query->get();

        // Variabel untuk menyimpan total sisa anggaran
        $totalSisaAnggaran = $totalAnggaran;

        // Variabel untuk menghitung total anggaran disetujui
        $totalAnggaranDisetujui = 0;

        // Memproses data untuk tampilan
        $data = $lpjData->map(function($lpj) use (&$totalSisaAnggaran, &$totalAnggaranDisetujui) {
            $totalAnggaranOrganisasi = $lpj->proker->organisasi->anggarans->sum('total_anggaran');
            $sisaAnggaran = $totalAnggaranOrganisasi - $lpj->dana_disetujui;

            // Mengurangi total sisa anggaran dengan dana disetujui
            $totalSisaAnggaran -= $lpj->dana_disetujui;

            // Menambah ke total anggaran disetujui
            $totalAnggaranDisetujui += $lpj->dana_disetujui;

            return [
                'id' => $lpj->id,
                'nama_organisasi' => $lpj->proker->organisasi->nama_organisasi,
                'nama_proker' => $lpj->proker->nama_proker,
                'dana_diajukan' => $lpj->proker->dana_diajukan,
                'dana_disetujui' => $lpj->dana_disetujui,
                'sisa_anggaran' => $sisaAnggaran,
                'total_sisa_anggaran' => $totalSisaAnggaran,
            ];
        });

        // Filter data berdasarkan organisasi jika nama organisasi bukan 'semua'
        if ($namaOrganisasi != 'semua') {
            $dataFiltered = $data->filter(function($item) use ($namaOrganisasi) {
                return $item['nama_organisasi'] == $namaOrganisasi;
            });
        } else {
            $dataFiltered = $data;
        }

        // Data tambahan untuk laporan
        $ketAnggaran = [
            'total_anggaran_periode' => $totalAnggaran,
            'total_anggaran_disetujui' => $totalAnggaranDisetujui,
            'sisa_anggaran_periode' => $totalSisaAnggaran
        ];

        // Format tanggal periode untuk nama file
        $tglMulaiFormatted = Carbon::parse($tglSetAnggaran)->format('d-m-Y');
        $tglAkhirFormatted = Carbon::parse($endDate)->format('d-m-Y');
        $fileName = "laporan-anggaran-{$tglMulaiFormatted}-to-{$tglAkhirFormatted}.pdf";

        $pdf = PDF::loadView('pdf.laporan-anggaran-pdf', [
            'anggaran' => $dataFiltered,
            'ketAnggaran' => $ketAnggaran
        ]);

        // Mengunduh laporan PDF dengan nama file yang dinamis
        return $pdf->download($fileName);
    }

    
    public function setAnggaran(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'total_anggaran' => 'required|numeric',
            'jenis_periode' => 'required|in:bulan,tahun',
            'total_periode' => 'required|numeric|min:1',
            'tgl_mulai_periode' => 'required|date', // Validasi untuk tanggal mulai periode
        ]);
    
        // Proses penyimpanan atau pembaruan data
        try {
            // Cek apakah ada data setAnggaran yang sudah ada di database
            $setAnggaran = SetAnggaran::first(); // Mengambil data pertama yang ditemukan
    
            if ($setAnggaran) {
                // Jika data sudah ada, lakukan pembaruan
                $setAnggaran->total_anggaran = $request->input('total_anggaran');
                $setAnggaran->jenis_periode = $request->input('jenis_periode');
                $setAnggaran->total_periode = $request->input('total_periode');
                $setAnggaran->tgl_mulai_periode = $request->input('tgl_mulai_periode'); // Perbarui tanggal mulai periode
                $setAnggaran->save(); // Simpan perubahan data
                $message = 'Anggaran berhasil diperbarui!';
            } else {
                // Jika data belum ada, buat data baru
                $setAnggaran = new SetAnggaran(); // Menggunakan model SetAnggaran
                $setAnggaran->total_anggaran = $request->input('total_anggaran');
                $setAnggaran->jenis_periode = $request->input('jenis_periode');
                $setAnggaran->total_periode = $request->input('total_periode');
                $setAnggaran->tgl_mulai_periode = $request->input('tgl_mulai_periode'); // Simpan tanggal mulai periode baru
                $setAnggaran->save(); // Simpan data baru ke dalam tabel set_anggaran
                $message = 'Anggaran berhasil disimpan!';
            }
    
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan anggaran: ' . $e->getMessage());
        }
    }
    
}
