<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $table = 'tb_proposal';

    protected $fillable = [
        'file_proposal', 'status', 'catatan', 'id_proker'
    ];

    public function proker()
    {
        return $this->belongsTo(Proker::class, 'id_proker');
    }
}
