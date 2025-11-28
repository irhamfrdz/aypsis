<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembayaranPranotaUangJalanBongkaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembayaran_pranota_uang_jalan_bongkarans';

    protected $fillable = [
        'pranota_uang_jalan_bongkaran_id',
        'nomor_pembayaran',
        'nomor_cetakan',
        'tanggal_pembayaran',
        'bank',
        'jenis_transaksi',
        'total_pembayaran',
        'total_tagihan_penyesuaian',
        'total_tagihan_setelah_penyesuaian',
        'alasan_penyesuaian',
        'keterangan',
        'status_pembayaran',
        'bukti_pembayaran',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'total_pembayaran' => 'decimal:2',
        'total_tagihan_penyesuaian' => 'decimal:2',
        'total_tagihan_setelah_penyesuaian' => 'decimal:2'
    ];

    // Backward compatibility: relation to pranota bongkaran (single pranota stored per record)
    public function pranotaUangJalanBongkaran()
    {
        return $this->belongsTo(PranotaUangJalanBongkaran::class, 'pranota_uang_jalan_bongkaran_id');
    }

    public function items()
    {
        return $this->hasMany(PembayaranPranotaUangJalanBongkaranItem::class, 'pembayaran_pranota_uang_jalan_bongkaran_id');
    }
}
