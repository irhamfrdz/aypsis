<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranAktivitasLainnyaItem extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_aktivitas_lainnya_items';

    protected $fillable = [
        'pembayaran_id',
        'aktivitas_id',
        'nominal',
        'keterangan'
    ];

    protected $casts = [
        'nominal' => 'decimal:2'
    ];

    /**
     * Relationship dengan pembayaran
     */
    public function pembayaran()
    {
        return $this->belongsTo(PembayaranAktivitasLainnya::class, 'pembayaran_id');
    }

    /**
     * Relationship dengan aktivitas
     */
    public function aktivitas()
    {
        return $this->belongsTo(AktivitasLainnya::class, 'aktivitas_id');
    }
}
