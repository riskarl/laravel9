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
}
