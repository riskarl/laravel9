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
        $lpj = LPJ::with('proker.organisasi')->find($lpj_id);
        if (!$lpj) {
            return false;
        }

        $isUpdated = false; // Variable to check if there are any updates

        // Logic to update status
        if ($lpj->status_flow_lpj == null || $lpj->status_flow_lpj == 0 || $lpj->status_flow_lpj == '') {
            if ($jabatan_id == 5) {
                if ($organisasi === 'BEM') {
                    $lpj->status_flow_lpj = 3;
                } else {
                    $lpj->status_flow_lpj = 2;
                }
                $lpj->status = 'Approved by Ketua ' . $organisasi;
                $isUpdated = $lpj->save();
            }
        } elseif ($lpj->status_flow_lpj == 2 && $organisasi == 'BEM' && $jabatan_id == 5) {
            $lpj->status_flow_lpj = 3;
            $lpj->status = 'Approved by Ketua ' . $organisasi;
            $isUpdated = $lpj->save();
        } elseif ($jabatan_id == 5 && $lpj->status_flow_lpj == 3 && $organisasi == 'BPM') {
            $lpj->status_flow_lpj = 4;
            $lpj->status = 'Approved by Ketua ' . $organisasi;
            $isUpdated = $lpj->save();
        } elseif ($jabatan_id == 4 && $lpj->status_flow_lpj == 4) {
            $lpj->status_flow_lpj = 5;
            $lpj->status = 'Approved by Pembina ' . $organisasi;
            $isUpdated = $lpj->save();
        } elseif ($jabatan_id == 8 && $lpj->status_flow_lpj == 5 && stripos($lpj->proker->organisasi->nama_organisasi, 'HIMA') !== false) {
            $lpj->status_flow_lpj = 6;
            $lpj->status = 'Approved by ' . $jabatan;
            $isUpdated = $lpj->save();
        } elseif ($jabatan_id == 3 && $lpj->status_flow_lpj == 6 && stripos($lpj->proker->organisasi->nama_organisasi, 'HIMA') !== false) {
            $lpj->status_flow_lpj = 7;
            $lpj->status = 'Approved by ' . $jabatan;
            $isUpdated = $lpj->save();
        } elseif ($jabatan_id == 2 && (($lpj->status_flow_lpj == 7 && stripos($lpj->proker->organisasi->nama_organisasi, 'HIMA') !== false) || ($lpj->status_flow_lpj == 5 && stripos($lpj->proker->organisasi->nama_organisasi, 'HIMA') === false))) {
            $lpj->status_flow_lpj = 8;
            $lpj->status = 'Approved by ' . $jabatan;
            $isUpdated = $lpj->save();
        }

        // If there is an update, collect signatures
        if ($isUpdated) {
            $ttdList = $this->collectSignatures($lpj, $organisasi, $jabatan_id);
            return $ttdList;
        }

        return false;
    }

    private function collectSignatures($proposal, $organisasi, $jabatan_id)
    {
        $ttdList = [];

        // Tentukan pengguna yang tanda tangannya perlu dikumpulkan berdasarkan organisasi dan status_flow
        $users = [];

        if ($proposal->proker->organisasi->nama_organisasi == 'BEM') {
            $users = [
                User::where('jabatan_id', 5)->where('organization', 'BEM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 5)->where('organization', 'BPM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 4)->where('organization', 'BEM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 2)
                        ->whereNotNull('ttd')
                        ->where('role', '<>', 1)
                        ->first(),
                User::where('jabatan_id', 1)->whereNotNull('ttd')->first(),
            ];
        } elseif (stripos($proposal->proker->organisasi->nama_organisasi, 'UKM') !== false) {
            $users = [
                User::where('jabatan_id', 5)->where('organization', $proposal->proker->organisasi->nama_organisasi)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 5)->where('organization', 'BEM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 5)->where('organization', 'BPM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 4)->where('organization', $proposal->proker->organisasi->nama_organisasi)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 2)
                        ->whereNotNull('ttd')
                        ->where('role', '<>', 1)
                        ->first(),
                User::where('jabatan_id', 1)->whereNotNull('ttd')->first(),
            ];
        } elseif (stripos($proposal->proker->organisasi->nama_organisasi, 'HIMA') !== false) {
            $users = [
                User::where('jabatan_id', 5)->where('organization', $proposal->proker->organisasi->nama_organisasi)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 5)->where('organization', 'BEM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 5)->where('organization', 'BPM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 4)->where('organization', $proposal->proker->organisasi->nama_organisasi)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 15)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 3)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 2)
                        ->whereNotNull('ttd')
                        ->where('role', '<>', 1)
                        ->first(),
                User::where('jabatan_id', 1)->whereNotNull('ttd')->first(),
            ];
        }

        $ttdFolderPath = public_path('ttd');

        // Tambahkan pengguna ke daftar ttd jika mereka memiliki ttd yang valid atau null jika tidak
        foreach ($users as $user) {
            if ($user) {
                $ttdPath = $ttdFolderPath . '/' . $user->ttd;
                if ($user->ttd && file_exists($ttdPath)) {
                    $ttdList[] = [
                        'nama' => $user->name,
                        'role' => $user->role,
                        'code_jabatan' => $user->jabatan->code_jabatan,
                        'organisasi' => $user->organization,
                        'jabatan' => $user->jabatan->jabatan,
                        'code_id' => $user->code_id,
                        'number_id' => $user->number_id,
                        'ttd' => $ttdPath,
                    ];
                } else {
                    // Tambahkan null jika file ttd tidak ditemukan
                    $ttdList[] = [
                        'nama' => $user->name,
                        'role' => $user->role,
                        'code_jabatan' => $user->jabatan->code_jabatan,
                        'organisasi' => $user->organization,
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

    public function signatureCreateLpj($jabatan_id, $lpj_id, $jabatan)
    {
        $lpj = LPJ::with('proker.organisasi')->find($lpj_id);

        if (!$lpj) {
            return false;
        }

        if ($jabatan_id == 1 && $lpj->status_flow_lpj == 8) {
            $lpj->status_flow_lpj = 9;
            $lpj->status = 'Approved by ' . $jabatan;
            $lpj->save();

            $organization = $lpj->proker->organisasi->nama_organisasi;
            $ttdList = [];

            // Determine the appropriate users to collect signatures from based on the organization
            $users = [];

            if ($lpj->proker->organisasi->nama_organisasi == 'BEM') {
                $users = [
                    User::where('jabatan_id', 5)->where('organization', 'BEM')->whereNotNull('ttd')->first(),
                    User::where('jabatan_id', 5)->where('organization', 'BPM')->whereNotNull('ttd')->first(),
                    User::where('jabatan_id', 4)->where('organization', 'BEM')->whereNotNull('ttd')->first(),
                    User::where('jabatan_id', 2)
                            ->whereNotNull('ttd')
                            ->where('role', '<>', 1)
                            ->first(),
                    User::where('jabatan_id', 1)->whereNotNull('ttd')->first(),
                ];
            } elseif (stripos($lpj->proker->organisasi->nama_organisasi, 'UKM') !== false) {
                $users = [
                    User::where('jabatan_id', 5)->where('organization', $lpj->proker->organisasi->nama_organisasi)->whereNotNull('ttd')->first(),
                    User::where('jabatan_id', 5)->where('organization', 'BEM')->whereNotNull('ttd')->first(),
                    User::where('jabatan_id', 5)->where('organization', 'BPM')->whereNotNull('ttd')->first(),
                    User::where('jabatan_id', 4)->where('organization', $lpj->proker->organisasi->nama_organisasi)->whereNotNull('ttd')->first(),
                    User::where('jabatan_id', 2)
                            ->whereNotNull('ttd')
                            ->where('role', '<>', 1)
                            ->first(),
                    User::where('jabatan_id', 1)->whereNotNull('ttd')->first(),
                ];
            } elseif (stripos($lpj->proker->organisasi->nama_organisasi, 'HIMA') !== false) {
                $users = [
                    User::where('jabatan_id', 5)->where('organization', $lpj->proker->organisasi->nama_organisasi)->whereNotNull('ttd')->first(),
                    User::where('jabatan_id', 5)->where('organization', 'BEM')->whereNotNull('ttd')->first(),
                    User::where('jabatan_id', 5)->where('organization', 'BPM')->whereNotNull('ttd')->first(),
                    User::where('jabatan_id', 4)->where('organization', $lpj->proker->organisasi->nama_organisasi)->whereNotNull('ttd')->first(),
                    User::where('jabatan_id', 15)->whereNotNull('ttd')->first(),
                    User::where('jabatan_id', 3)->whereNotNull('ttd')->first(),
                    User::where('jabatan_id', 2)
                            ->whereNotNull('ttd')
                            ->where('role', '<>', 1)
                            ->first(),
                    User::where('jabatan_id', 1)->whereNotNull('ttd')->first(),
                ];
            }

            $ttdFolderPath = public_path('ttd');

            foreach ($users as $user) {
                $ttdPath = $ttdFolderPath . '/' . $user->ttd;
                if ($user->ttd && file_exists($ttdPath)) {
                    $ttdList[] = [
                        'nama' => $user->name,
                        'role' => $user->role,
                        'code_jabatan' => $user->jabatan->code_jabatan,
                        'organisasi' => $user->organization,
                        'jabatan' => $user->jabatan->jabatan,
                        'code_id' => $user->code_id,
                        'number_id' => $user->number_id,
                        'ttd' => $ttdPath,
                    ];
                } else {
                    $ttdList[] = [
                        'nama' => $user->name,
                        'role' => $user->role,
                        'code_jabatan' => $user->jabatan->code_jabatan,
                        'organisasi' => $user->organization,
                        'jabatan' => $user->jabatan->jabatan,
                        'code_id' => $user->code_id,
                        'number_id' => $user->number_id,
                        'ttd' => null,
                    ];
                }
            }

            return $ttdList;
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
                User::where('jabatan_id', 2)
                        ->whereNotNull('ttd')
                        ->where('role', '<>', 1)
                        ->first(),
                User::where('jabatan_id', 1)->whereNotNull('ttd')->first(),
            ];
        } else if (stripos($organisasiName, 'UKM') !== false) {
            $users = [
                User::where('jabatan_id', 5)->where('organization', $organisasiName)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 5)->where('organization', 'BEM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 5)->where('organization', 'BPM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 4)->where('organization', $organisasiName)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 2)
                        ->whereNotNull('ttd')
                        ->where('role', '<>', 1)
                        ->first(),
                User::where('jabatan_id', 1)->whereNotNull('ttd')->first(),
            ];
        } else if (stripos($organisasiName, 'HIMA') !== false) {
            $users = [
                User::where('jabatan_id', 5)->where('organization', $organisasiName)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 5)->where('organization', 'BEM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 5)->where('organization', 'BPM')->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 4)->where('organization', $organisasiName)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 15)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 3)->whereNotNull('ttd')->first(),
                User::where('jabatan_id', 2)
                        ->whereNotNull('ttd')
                        ->where('role', '<>', 1)
                        ->first(),
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
