<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaSupir extends Model
{
    protected $table = 'pembayaran_pranota_supir';
    protected $guarded = [];

    protected $casts = [
        'tanggal_kas' => 'date',
        'tanggal_pembayaran' => 'date',
    ];

    public function pranotas()
    {
        return $this->belongsToMany(PranotaSupir::class, 'pembayaran_pranota_supir_pranota_supir', 'pembayaran_pranota_supir_id', 'pranota_supir_id');
    }
}
