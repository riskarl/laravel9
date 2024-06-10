<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MappingCheck extends Model
{
    /**
     * Update status_flow based on jabatan_id and organisasi.
     *
     * @param  int  $proposal_id  // Mengganti parameter proker_id dengan proposal_id
     * @param  int  $jabatan_id
     * @param  string  $organisasi
     * @return bool
     */
    public function updateStatusFlow($proposal_id, $jabatan_id, $organisasi, $jabatan = null)
    {
        // Mengambil proposal yang terkait dengan proposal_id
        $proposal = Proposal::with('proker.organisasi')->find($proposal_id);
    
        // Jika tidak ditemukan proposal, return false
        if (!$proposal) {
            return false;
        }
    
        $isUpdated = false; // Variabel untuk mengecek apakah ada perubahan
    
        // Logika update status
        if ($proposal->status_flow == null || $proposal->status_flow == 0 || $proposal->status_flow == '') {
            if ($jabatan_id == 5) {
                if ($organisasi === 'BEM') {
                    $proposal->status_flow = 3;
                } else {
                    $proposal->status_flow = 2;
                }
                $proposal->status = 'Approved by Ketua ' . $organisasi;
                $isUpdated = $proposal->save();
            }
        } elseif ($proposal->status_flow == 2 && $organisasi == 'BEM' && $jabatan_id == 5) {
            $proposal->status_flow = 3;
            $proposal->status = 'Approved by Ketua ' . $organisasi;
            $isUpdated = $proposal->save();
        } elseif ($jabatan_id == 5 && $proposal->status_flow == 3 && $organisasi == 'BPM') {
            $proposal->status_flow = 4;
            $proposal->status = 'Approved by Ketua ' . $organisasi;
            $isUpdated = $proposal->save();
        } elseif ($jabatan_id == 4 && $proposal->status_flow == 4) {
            $proposal->status_flow = 5;
            $proposal->status = 'Approved by Pembina ' . $organisasi;
            $isUpdated = $proposal->save();
        } elseif ($jabatan_id == 8 && $proposal->status_flow == 5 && stripos($proposal->proker->organisasi->nama_organisasi, 'HIMA') !== false) {
            $proposal->status_flow = 6;
            $proposal->status = 'Approved by ' . $jabatan;
            $isUpdated = $proposal->save();
        } elseif ($jabatan_id == 3 && $proposal->status_flow == 6 && stripos($proposal->proker->organisasi->nama_organisasi, 'HIMA') !== false) {
            $proposal->status_flow = 7;
            $proposal->status = 'Approved by ' . $jabatan;
            $isUpdated = $proposal->save();
        } elseif ($jabatan_id == 2 && (($proposal->status_flow == 7 && stripos($proposal->proker->organisasi->nama_organisasi, 'HIMA') !== false) || ($proposal->status_flow == 5 && stripos($proposal->proker->organisasi->nama_organisasi, 'HIMA') === false))) {
            $proposal->status_flow = 8;
            $proposal->status = 'Approved by ' . $jabatan;
            $isUpdated = $proposal->save();
        }
    
        // Jika ada perubahan status, kumpulkan tanda tangan
        if ($isUpdated) {
            $ttdList = $this->collectSignatures($proposal, $organisasi, $jabatan_id);
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
    
    public function signatureCreate($jabatan_id, $proposal_id, $jabatan)
    {
        $proposal = Proposal::with('proker.organisasi')->find($proposal_id);

        // Jika tidak ditemukan proposal, return false
        if (!$proposal) {
            return false;
        }

        if ($jabatan_id == 1 && $proposal->status_flow == 8) {

            $proposal->status_flow = 9;
            $proposal->status = 'Approved by ' . $jabatan;


            $proposal->save();

            if ($proposal->proker->organisasi->nama_organisasi == 'BEM') {
                $ttdList = [];

                // Mendapatkan user berdasarkan jabatan dan organisasi
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

                $ttdFolderPath = public_path('ttd');

                // Menambahkan pengguna ke daftar ttd jika mereka memiliki ttd yang valid atau null jika tidak
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
                            // Menambahkan null jika file ttd tidak ditemukan
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

            if (stripos($proposal->proker->organisasi->nama_organisasi, 'UKM') !== false) {
                $ttdList = [];

                // Mendapatkan user berdasarkan jabatan dan organisasi
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

                $ttdFolderPath = public_path('ttd');

                // Menambahkan pengguna ke daftar ttd jika mereka memiliki ttd yang valid atau null jika tidak
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
                            // Menambahkan null jika file ttd tidak ditemukan
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

            if (stripos($proposal->proker->organisasi->nama_organisasi, 'HIMA') !== false) {
                $ttdList = [];

                // Mendapatkan user berdasarkan jabatan dan organisasi
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

                $ttdFolderPath = public_path('ttd');

                // Menambahkan pengguna ke daftar ttd jika mereka memiliki ttd yang valid atau null jika tidak
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
                            // Menambahkan null jika file ttd tidak ditemukan
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

        return false;

    }

    public function updateRevisi($proposal_id, $jabatan_id, $organisasi, $jabatan = null, $catatan = null)
    {
        $proposal = Proposal::with('proker.organisasi')->find($proposal_id);

        // Jika tidak ditemukan proposal, return false
        if (!$proposal) {
            return false;
        }

        // Mengupdate status_flow menjadi 0
        $proposal->status_flow = 1;

        // Mengupdate catatan jika catatan disediakan
        if ($catatan) {
            $proposal->catatan = $catatan;
        }

        // Mengganti 'approve by' menjadi 'revisi by' dan menambahkan jabatan dan organisasi
        if ($jabatan_id == 5) {
            if ($organisasi === 'BEM') {
                $proposal->status = 'Revisi by Ketua ' . $organisasi;
            } else {
                $proposal->status = 'Revisi by Ketua ' . $organisasi;
            }
        } else if ($jabatan_id == 4) {
            $proposal->status = 'Revisi by Pembina ' . $organisasi;
        } else {
            // Mengganti status berdasarkan jabatan dan jabatan_id
            $proposal->status = 'Revisi by ' . $jabatan;
        }

        // Simpan perubahan pada proposal
        return $proposal->save();
    }


}
