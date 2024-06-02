<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LPJ extends Model
{
    use HasFactory;

    protected $table = 'tb_lpj';

    //Menetapkan primaryKey untuk model ini sesuai dengan kolom 'id' di database
    protected $primaryKey = 'id';

    // Menentukan bahwa model ini memiliki timestamps
    public $timestamps = true;

    protected $fillable = [
        'file_lpj',
        'id_proker',
        'file_lpj',
        'status',
        'catatan'

    ];
    public function proker()
    {
        return $this->belongsTo(Proker::class, 'id_proker');
    }

}
