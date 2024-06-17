<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LPJ extends Model
{
    use HasFactory;

    protected $table = 'tb_lpj';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'id_proker',
        'file_lpj',
        'status',
        'catatan',
        'dana_disetujui',
        'status_flow_lpj'
    ];

    public function proker()
    {
        return $this->belongsTo(Proker::class, 'id_proker');
    }
}
