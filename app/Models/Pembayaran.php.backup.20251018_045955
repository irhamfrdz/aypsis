<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pembayaran',
        'tanggal_pembayaran',
        'total_pembayaran',
        'metode_pembayaran',
        'catatan_pembayaran',
    ];

    /**
     * Pranota Supir yang termasuk dalam pembayaran ini.
     */
    public function pranotaSupirs()
    {
        return $this->belongsToMany(PranotaSupir::class, 'pembayaran_pranota_supir');
    }
}
