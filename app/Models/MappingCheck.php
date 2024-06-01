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

        // Cek status_flow saat ini harus 0, null, atau empty
        if ($proposal->status_flow == null || $proposal->status_flow == 0 || $proposal->status_flow == '') {
            // Cek jabatan_id dan organisasi, kemudian update status_flow
            if ($jabatan_id == 5) {

                if ($organisasi === 'BEM') {
                    $proposal->status_flow = 3;
                } else {
                    $proposal->status_flow = 2;
                }
                $proposal->status = 'Approved by Ketua ' . $organisasi;

                // Simpan perubahan pada proposal
                return $proposal->save();
            }

        }

        // Kondisi tambahan untuk status_flow awalnya 2 dan organisasi bukan BEM
        if ($proposal->status_flow == 2 && $organisasi == 'BEM' && $jabatan_id == 5) {
            // Update status_flow menjadi 3
            $proposal->status_flow = 3;
            $proposal->status = 'Approved by Ketua ' . $organisasi;

            // Simpan perubahan pada proposal
            return $proposal->save();
        }

        // Kondisi tambahan jika jabatan_id nya 5, status_flow nya 3, dan organisasi nya BPM
        if ($jabatan_id == 5 && $proposal->status_flow == 3 && $organisasi == 'BPM') {
            // Update status_flow menjadi 4
            $proposal->status_flow = 4;
            $proposal->status = 'Approved by Ketua ' . $organisasi;

            // Simpan perubahan pada proposal
            return $proposal->save();
        }

        if ($jabatan_id == 4 && $proposal->status_flow == 4 ) {
            $proposal->status_flow = 5;
            $proposal->status = 'Approved by Pembina ' . $organisasi;

            return $proposal->save();
        }

        $containsHima = strpos($proposal->proker->organisasi->nama_organisasi, 'HIMA') !== false;

        if ($jabatan_id == 8 && $proposal->status_flow == 5 && $containsHima) {
            $proposal->status_flow = 6;
            $proposal->status = 'Approved by ' . $jabatan;

            return $proposal->save();
        }
        

        if ($jabatan_id == 3 && $proposal->status_flow == 6 && $containsHima) {
            $proposal->status_flow = 7;
            $proposal->status = 'Approved by ' . $jabatan;

            return $proposal->save();
        }

        if ($jabatan_id == 2 && (($proposal->status_flow == 7 && $containsHima) || ($proposal->status_flow == 5 && !$containsHima))) {

            $proposal->status_flow = 8;
            $proposal->status = 'Approved by ' . $jabatan;

            return $proposal->save();
        }

        return false;
    }

    public function signatureCreate($jabatan_id, $proposal_id)
    {
        $proposal = Proposal::with('proker.organisasi')->find($proposal_id);

        // Jika tidak ditemukan proposal, return false
        if (!$proposal) {
            return false;
        }

        if ($jabatan_id == 1 && $proposal->status_flow == 8) {
            
            if ($proposal->proker->organisasi->nama_organisasi == 'BEM') {
                $ttdList = [];

                // jabatan_id 5 yang organisasi BEM
                $user1 = User::where('jabatan_id', 5)->where('organization', 'BEM')->first();

                // jabatan_id 5 yang organisasi BPM
                $user2 = User::where('jabatan_id', 5)->where('organization', 'BPM')->first();

                // jabatan_id 4 yang organisasi BEM
                $user3 = User::where('jabatan_id', 4)->where('organization', 'BEM')->first();

                // jabatan_id 2
                $user4 = User::where('jabatan_id', 2)->first();

                // jabatan_id 1
                $user5 = User::where('jabatan_id', 1)->first();

                $ttdFolderPath = public_path('ttd');

                if ($user1) $ttdList[] = [
                    'nama' => $user1->name,
                    'jabatan' => $user1->jabatan->jabatan,
                    'ttd' => $ttdFolderPath . '/' . $user1->ttd,
                ];

                if ($user2) $ttdList[] = [
                    'nama' => $user2->name,
                    'jabatan' => $user2->jabatan->jabatan,
                    'ttd' => $ttdFolderPath . '/' . $user2->ttd,
                ];

                if ($user3) $ttdList[] = [
                    'nama' => $user3->name,
                    'jabatan' => $user3->jabatan->jabatan,
                    'ttd' => $ttdFolderPath . '/' . $user3->ttd,
                ];

                if ($user4) $ttdList[] = [
                    'nama' => $user4->name,
                    'jabatan' => $user4->jabatan->jabatan,
                    'ttd' => $ttdFolderPath . '/' . $user4->ttd,
                ];

                if ($user5) $ttdList[] = [
                    'nama' => $user5->name,
                    'jabatan' => $user5->jabatan->jabatan,
                    'ttd' => $ttdFolderPath . '/' . $user5->ttd,
                ];

                return $ttdList;
            }

            if ($proposal->proker->organisasi->nama_organisasi == 'UKM') {
                $ttdList = [];

                $user1 = User::where('jabatan_id', 5)->where('organization', 'UKM')->first();
                // jabatan_id 5 yang organisasi BEM
                $user2 = User::where('jabatan_id', 5)->where('organization', 'BEM')->first();

                // jabatan_id 5 yang organisasi BPM
                $user3 = User::where('jabatan_id', 5)->where('organization', 'BPM')->first();

                // jabatan_id 4 yang organisasi BEM
                $user4 = User::where('jabatan_id', 4)->where('organization', 'BEM')->first();

                // jabatan_id 2
                $user5 = User::where('jabatan_id', 2)->first();

                // jabatan_id 1
                $user6 = User::where('jabatan_id', 1)->first();

                $ttdFolderPath = public_path('ttd');

                if ($user1) $ttdList[] = [
                    'nama' => $user1->name,
                    'jabatan' => $user1->jabatan->jabatan,
                    'ttd' => $ttdFolderPath . '/' . $user1->ttd,
                ];

                if ($user2) $ttdList[] = [
                    'nama' => $user2->name,
                    'jabatan' => $user2->jabatan->jabatan,
                    'ttd' => $ttdFolderPath . '/' . $user2->ttd,
                ];

                if ($user3) $ttdList[] = [
                    'nama' => $user3->name,
                    'jabatan' => $user3->jabatan->jabatan,
                    'ttd' => $ttdFolderPath . '/' . $user3->ttd,
                ];

                if ($user4) $ttdList[] = [
                    'nama' => $user4->name,
                    'jabatan' => $user4->jabatan->jabatan,
                    'ttd' => $ttdFolderPath . '/' . $user4->ttd,
                ];

                if ($user5) $ttdList[] = [
                    'nama' => $user5->name,
                    'jabatan' => $user5->jabatan->jabatan,
                    'ttd' => $ttdFolderPath . '/' . $user5->ttd,
                ];

                if ($user6) $ttdList[] = [
                    'nama' => $user5->name,
                    'jabatan' => $user5->jabatan->jabatan,
                    'ttd' => $ttdFolderPath . '/' . $user6->ttd,
                ];

                return $ttdList;
            }
        }

        return false;
    
    }

    public function updateRevisi($proposal_id, $jabatan_id, $organisasi, $jabatan = null, $catatan = null){
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
