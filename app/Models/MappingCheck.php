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
    public function updateStatusFlow($proposal_id, $jabatan_id, $organisasi)
    {
        // Mengambil proposal yang terkait dengan proposal_id
        $proposal = Proposal::find($proposal_id);

        // Jika tidak ditemukan proposal, return false
        if (!$proposal) {
            return false;
        }

        // Cek jabatan_id dan organisasi, kemudian update status_flow
        if ($jabatan_id == 5) {
            if ($organisasi === 'BEM') {
                $proposal->status_flow = 3;
            } else {
                $proposal->status_flow = 2;
            }
        } else {
            // Jika jabatan_id tidak sesuai, tidak perlu update
            return false;
        }

        // Simpan perubahan pada proposal
        return $proposal->save();
    }
}
