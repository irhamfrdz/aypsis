<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class PembayaranAktivitasLainnyaSupir extends Model
{
    use HasFactory, Auditable;

    protected $table = 'pembayaran_aktivitas_lainnya_supir';

    protected $fillable = [
        'pembayaran_id',
        'supir_id',
        'jumlah_uang_muka',
        'keterangan'
    ];

    protected $casts = [
        'jumlah_uang_muka' => 'decimal:2'
    ];

    /**
     * Relationship dengan pembayaran
     */
    public function pembayaran()
    {
        return $this->belongsTo(PembayaranAktivitasLainnya::class, 'pembayaran_id');
    }

    /**
     * Relationship dengan supir
     */
    public function supir()
    {
        return $this->belongsTo(Supir::class, 'supir_id');
    }

    /**
     * Get supir name attribute for easy access
     */
    public function getSupirNameAttribute()
    {
        return $this->supir ? $this->supir->nama_lengkap : null;
    }
}