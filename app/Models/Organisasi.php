<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisasi extends Model
{
    use HasFactory;
    protected $table = 'tb_organisasi';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_organisasi', 'nama_pembina', 'nama_ketua', 'periode'];

    public function proker()
    {
        return $this->hasMany(Proker::class, 'nama_organisasi', 'nama_organisasi');
    }
    public function anggarans()
    {
        return $this->hasMany(Anggaran::class, 'id');
    }
}





