<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class PembayaranBiayaKapal extends Model
{
    use SoftDeletes, Auditable;

    protected $table = 'pembayaran_biaya_kapals';

    protected $fillable = [
        'nomor_pembayaran',
        'nomor_accurate',
        'tanggal_pembayaran',
        'bank',
        'jenis_transaksi',
        'total_pembayaran',
        'total_tagihan_penyesuaian',
        'alasan_penyesuaian',
        'keterangan',
        'status_pembayaran',
        'bukti_pembayaran',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'total_pembayaran' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(PembayaranBiayaKapalItem::class, 'pembayaran_biaya_kapal_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function biayaKapals()
    {
        return $this->belongsToMany(BiayaKapal::class, 'pembayaran_biaya_kapal_items', 'pembayaran_biaya_kapal_id', 'biaya_kapal_id')
                    ->withPivot('nominal')
                    ->withTimestamps();
    }
}
