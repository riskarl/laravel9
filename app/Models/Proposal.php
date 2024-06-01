<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $table = 'tb_proposal';

    // Menetapkan primaryKey untuk model ini sesuai dengan kolom 'id' di database
    protected $primaryKey = 'id';

    // Menentukan bahwa model ini memiliki timestamps
    public $timestamps = true;

    protected $fillable = [
        'file_proposal', 'status', 'catatan', 'id_proker', 'status_flow', 'pengesahan'
    ];

    public function proker()
    {
        return $this->belongsTo(Proker::class, 'id_proker');
    }
}
