<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proker extends Model
{
    use HasFactory;
    protected $table = 'tb_proker';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_organisasi',
        'nama_organisasi',
        'nama_proker',
        'nama_ketupel',
        'nim_ketupel',
        'tanggal',
        'tempat',
        'dana_diajukan',
        'ttd_ketupel',
    ];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'id_organisasi', 'id');
    }

    public function proposal()
    {
        return $this->hasOne(Proposal::class, 'id_proker');
    }

    public function rab()
    {
        return $this->hasOne(Rab::class, 'id_proker');
    }

    public function srpd()
    {
        return $this->hasOne(Rab::class, 'id_proker');
    }

    public function lpj()
    {
        return $this->hasOne(Lpj::class, 'id_proker');
    }
}
