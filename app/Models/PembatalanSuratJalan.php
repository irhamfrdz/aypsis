<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembatalanSuratJalan extends Model
{
    protected $table = 'pembatalan_surat_jalans';

    protected $fillable = [
        'surat_jalan_id',
        'surat_jalan_bongkaran_id',
        'tipe_sj',
        'no_surat_jalan',
        'nomor_pembayaran',
        'nomor_accurate',
        'tanggal_kas',
        'tanggal_pembayaran',
        'bank',
        'jenis_transaksi',
        'total_pembayaran',
        'total_tagihan_penyesuaian',
        'total_tagihan_setelah_penyesuaian',
        'alasan_penyesuaian',
        'keterangan',
        'alasan_batal',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal_kas' => 'date',
        'tanggal_pembayaran' => 'date',
        'total_pembayaran' => 'decimal:2',
        'total_tagihan_penyesuaian' => 'decimal:2',
        'total_tagihan_setelah_penyesuaian' => 'decimal:2',
    ];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id');
    }

    public function suratJalanBongkaran()
    {
        return $this->belongsTo(SuratJalanBongkaran::class, 'surat_jalan_bongkaran_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
