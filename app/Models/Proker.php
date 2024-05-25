<?php

namespace App\Models;

use App\Http\Controllers\LpjController;
use App\Http\Controllers\RabController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proker extends Model
{
    use HasFactory;
    protected $table = 'tb_proker';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_organisasi', 'nama_proker', 'nama_ketupel', 'tanggal', 'tempat', 'dana_diajukan'];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'nama_organisasi');
    }

    public function proposal()
    {
        return $this->hasOne(Proposal::class, 'id_proker');
    }

    public function lpj()
    {
        return $this->belongsTo(Lpj::class, 'lpj');
    }
}
