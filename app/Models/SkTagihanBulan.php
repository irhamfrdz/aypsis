<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class SkTagihanBulan extends Model
{
    use Auditable;

    protected $table = 'sk_tagihan_bulans';

    protected $fillable = [
        'kode_tagihan',
        'sewa_id',
        'bulan_ke',
        'tanggal_awal',
        'tanggal_akhir',
        'jumlah_hari',
        'tipe_tarif',
        'jumlah_tagihan_estimasi',
        'jumlah_tagihan_override',
        'status_bayar',
        'tanggal_tagihan',
        'tanggal_bayar',
        'nomor_invoice',
        'nomor_pranota',
        'tanggal_pranota',
        'jumlah_bayar',
        'ppn',
        'pph',
        'nomor_bayar',
        'keterangan_selisih',
    ];

    protected $casts = [
        'tanggal_awal' => 'date',
        'tanggal_akhir' => 'date',
        'tanggal_tagihan' => 'date',
        'tanggal_bayar' => 'date',
        'tanggal_pranota' => 'date',
        'jumlah_tagihan_estimasi' => 'integer',
        'jumlah_tagihan_override' => 'integer',
        'jumlah_bayar' => 'integer',
        'ppn' => 'integer',
        'pph' => 'integer',
        'bulan_ke' => 'integer',
        'jumlah_hari' => 'integer',
    ];

    public function sewa()
    {
        return $this->belongsTo(SkSewa::class, 'sewa_id');
    }

    public function invoiceGrups()
    {
        return $this->belongsToMany(SkInvoiceGrup::class, 'sk_invoice_grup_tagihans', 'tagihan_bulan_id', 'invoice_grup_id');
    }

    /**
     * Get the effective billing amount (override takes priority over estimate)
     */
    public function getJumlahTagihanEfektifAttribute(): int
    {
        return $this->jumlah_tagihan_override ?? $this->jumlah_tagihan_estimasi;
    }
}
