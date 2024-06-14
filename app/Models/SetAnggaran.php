<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetAnggaran extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'set_anggaran';

    // Primary Key
    protected $primaryKey = 'id_set_anggaran';

    // Kolom yang dapat diisi
    protected $fillable = [
        'total_anggaran',
        'jenis_periode',
        'total_periode',
        'tgl_mulai_periode'
    ];

    public $timestamps = true;
}
