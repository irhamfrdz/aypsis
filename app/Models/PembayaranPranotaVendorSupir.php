<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaVendorSupir extends Model
{
    protected $fillable = [
        'nomor_pembayaran',
        'tanggal_pembayaran',
        'vendor_id',
        'total_pembayaran',
        'metode_pembayaran',
        'bank',
        'no_referensi',
        'keterangan',
        'bukti_pembayaran',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(VendorSupir::class, 'vendor_id');
    }

    public function items()
    {
        return $this->hasMany(PembayaranPranotaVendorSupirItem::class, 'pembayaran_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
