<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model
{
    use HasFactory;

    protected $table = 'tb_anggaran';
    protected $primaryKey = 'id_anggaran';
    protected $fillable = ['id_anggaran', 'id_organisasi', 'jumlah_mhs', 'jumlah_anggaran', 'total_anggaran'];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'id_organisasi', 'id');
    }

    public function proker()
    {
        return $this->hasMany(Proker::class, 'id_organisasi', 'id_organisasi');
    }

    // Relasi ke LPJ melalui Proker
    public function lpjs()
    {
        return $this->hasManyThrough(LPJ::class, Proker::class, 'id_organisasi', 'id_proker', 'id_organisasi', 'id');
    }
}
