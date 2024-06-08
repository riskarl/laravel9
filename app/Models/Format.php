<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Format extends Model
{
    use HasFactory;

    protected $table = 'tb_format';
    protected $primaryKey = 'id_format';
    protected $fillable = ['id_format', 'jenis_format', 'file_format'];

}
