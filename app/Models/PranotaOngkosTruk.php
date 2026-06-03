<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PranotaOngkosTruk extends Model
{
    use Auditable, HasFactory;

    protected $table = 'pranota_ongkos_truks';

    protected $fillable = [
        'no_pranota',
        'tanggal_pranota',
        'total_nominal',
        'adjustment',
        'adjustments',
        'keterangan',
        'status',
        'status_pembayaran',
        'supir_id',
        'vendor_id',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
        'adjustments' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PranotaOngkosTrukItem::class, 'pranota_ongkos_truk_id');
    }

    public function pembayaranPranotaOngkosTruks()
    {
        return $this->belongsToMany(
            PembayaranPranotaOngkosTruk::class,
            'pembayaran_pranota_ongkos_truk_items',
            'pranota_ongkos_truk_id',
            'pembayaran_pranota_ongkos_truk_id'
        )->withPivot('subtotal')->withTimestamps();
    }
}
