<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MappingCheckLpj extends Model
{
    /**
     * Update status_flow_lpj based on jabatan_id and organisasi for LPJ.
     *
     * @param  int  $lpj_id
     * @param  int  $jabatan_id
     * @param  string  $organisasi
     * @param  string|null  $jabatan
     * @return bool
     */
    public function updateStatusFlowLpj($lpj_id, $jabatan_id, $organisasi, $jabatan = null)
    {
        // Mengambil lpj yang terkait dengan lpj_id
        $lpj = LPJ::with('proker.organisasi')->find($lpj_id);

        // Jika tidak ditemukan lpj, return false
        if (!$lpj) {
            return false;
        }

        // Cek status_flow_lpj saat ini harus null, 0, atau empty
        if ($lpj->status_flow_lpj == null || $lpj->status_flow_lpj == 0 || $lpj->status_flow_lpj == '') {
            // Cek jabatan_id dan organisasi, kemudian update status_flow_lpj
            if ($jabatan_id == 5) {
                if ($organisasi === 'BEM') {
                    $lpj->status_flow_lpj = 3;
                } else {
                    $lpj->status_flow_lpj = 2;
                }
                $lpj->status = 'Approved by Ketua ' . $organisasi;

                // Simpan perubahan pada lpj
                return $lpj->save();
            }
        }

        // Kondisi tambahan untuk status_flow_lpj awalnya 2 dan organisasi bukan BEM
        if ($lpj->status_flow_lpj == 2 && $organisasi == 'BEM' && $jabatan_id == 5) {
            // Update status_flow_lpj menjadi 3
            $lpj->status_flow_lpj = 3;
            $lpj->status = 'Approved by Ketua ' . $organisasi;

            // Simpan perubahan pada lpj
            return $lpj->save();
        }

        // Kondisi tambahan jika jabatan_id nya 5, status_flow_lpj nya 3, dan organisasi nya BPM
        if ($jabatan_id == 5 && $lpj->status_flow_lpj == 3 && $organisasi == 'BPM') {
            // Update status_flow_lpj menjadi 4
            $lpj->status_flow_lpj = 4;
            $lpj->status = 'Approved by Ketua ' . $organisasi;

            // Simpan perubahan pada lpj
            return $lpj->save();
        }

        if ($jabatan_id == 4 && $lpj->status_flow_lpj == 4) {
            $lpj->status_flow_lpj = 5;
            $lpj->status = 'Approved by Pembina ' . $organisasi;

            return $lpj->save();
        }

        $containsHima = strpos($lpj->proker->organisasi->nama_organisasi, 'HIMA') !== false;

        if ($jabatan_id == 8 && $lpj->status_flow_lpj == 5 && $containsHima) {
            $lpj->status_flow_lpj = 6;
            $lpj->status = 'Approved by ' . $jabatan;

            return $lpj->save();
        }

        if ($jabatan_id == 3 && $lpj->status_flow_lpj == 6 && $containsHima) {
            $lpj->status_flow_lpj = 7;
            $lpj->status = 'Approved by ' . $jabatan;

            return $lpj->save();
        }

        if ($jabatan_id == 2 && (($lpj->status_flow_lpj == 7 && $containsHima) || ($lpj->status_flow_lpj == 5 && !$containsHima))) {
            $lpj->status_flow_lpj = 8;
            $lpj->status = 'Approved by ' . $jabatan;

            return $lpj->save();
        }

        return false;
    }

    public function signatureCreateLpj($jabatan_id, $lpj_id, $jabatan)
    {
        $lpj = LPJ::with('proker.organisasi')->find($lpj_id);

        // Jika tidak ditemukan lpj, return false
        if (!$lpj) {
            return false;
        }

        if ($jabatan_id == 1 && $lpj->status_flow_lpj == 8) {
            $lpj->status_flow_lpj = 9;
            $lpj->status = 'Approved by ' . $jabatan;
            $lpj->save();

            return true;
        }

        return false;
    }

    public function updateRevisiLpj($lpj_id, $jabatan_id, $organisasi, $jabatan = null, $catatan = null)
    {
        $lpj = LPJ::with('proker.organisasi')->find($lpj_id);

        // Jika tidak ditemukan lpj, return false
        if (!$lpj) {
            return false;
        }

        // Mengupdate status_flow_lpj menjadi 1
        $lpj->status_flow_lpj = 1;

        // Mengupdate catatan jika catatan disediakan
        if ($catatan) {
            $lpj->catatan = $catatan;
        }

        // Mengganti 'approve by' menjadi 'revisi by' dan menambahkan jabatan dan organisasi
        if ($jabatan_id == 5) {
            $lpj->status = 'Revisi by Ketua ' . $organisasi;
        } else if ($jabatan_id == 4) {
            $lpj->status = 'Revisi by Pembina ' . $organisasi;
        } else {
            $lpj->status = 'Revisi by ' . $jabatan;
        }

        return $lpj->save();
    }

    private function getSignatureList($organisasiName)
    {
        $ttdList = [];

        if ($organisasiName == 'BEM') {
            $users = [
                User::where('jabatan_id', 5)->where('organization', 'BEM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 5)->where('organization', 'BPM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 4)->where('organization', 'BEM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 2)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 1)->whereNotNull('ttd')->first(),
            ];
        } else if (strpos($organisasiName, 'UKM') !== false) {
            $users = [
                User::where('jabatan_id', 5)->where('organization', $organisasiName)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 5)->where('organization', 'BEM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 5)->where('organization', 'BPM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 4)->where('organization', $organisasiName)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 2)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 1)->whereNotNull('ttd')->first(),
            ];
        } else if (strpos($organisasiName, 'HIMA') !== false) {
            $users = [
                User::where('jabatan_id', 5)->where('organization', $organisasiName)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 5)->where('organization', 'BEM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 5)->where('organization', 'BPM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 4)->where('organization', $organisasiName)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 15)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 3)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 2)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 1)->whereNotNull('ttd')->first(),
            ];
        }

        $ttdFolderPath = public_path('ttd');

        foreach ($users as $user) {
            if ($user) {
                $ttdPath = $ttdFolderPath . '/' . $user->ttd;
                if ($user->ttd && file_exists($ttdPath)) {
                    $ttdList[] = [
                        'nama' => $user->name,
                        'jabatan' => $user->jabatan->jabatan,
                        'code_id' => $user->code_id,
                        'number_id' => $user->number_id,
                        'ttd' => $ttdPath,
                    ];
                } else {
                    $ttdList[] = [
                        'nama' => $user->name,
                        'jabatan' => $user->jabatan->jabatan,
                        'code_id' => $user->code_id,
                        'number_id' => $user->number_id,
                        'ttd' => null,
                    ];
                }
            }
        }

        return $ttdList;
    }
}
