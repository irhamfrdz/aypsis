<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaVendorSupir extends Model
{
    protected $fillable = [
        'nomor_pembayaran',
        'nomor_accurate', // Added nomor_accurate
        'tanggal_pembayaran',
        'vendor_id',
        'total_pembayaran',
        'total_tagihan_penyesuaian',
        'total_tagihan_setelah_penyesuaian',
        'alasan_penyesuaian',
        'metode_pembayaran',
        'bank',
        'akun_biaya_coa_id',
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

    public function akunBiayaCoa()
    {
        return $this->belongsTo(Coa::class, 'akun_biaya_coa_id');
    }

    public function akunBiayaItems()
    {
        return $this->hasMany(PembayaranPranotaVendorSupirCoaItem::class, 'pembayaran_id');
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
