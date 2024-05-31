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
        $proposal = Proposal::with('proker')->find($proposal_id);

        // Jika tidak ditemukan proposal, return false
        if (!$proposal) {
            return false;
        }

        var_dump($proposal->proker);die;
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

        $containsHima = strpos($proposal->proker->nama_organisasi, 'HIMA') !== false;

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

}
