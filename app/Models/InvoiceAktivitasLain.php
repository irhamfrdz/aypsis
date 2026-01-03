<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class InvoiceAktivitasLain extends Model
{
    use HasFactory, Auditable;

    protected $table = 'invoice_aktivitas_lain';

    protected $fillable = [
        'nomor_invoice',
        'tanggal_invoice',
        'jenis_aktivitas',
        'sub_jenis_kendaraan',
        'nomor_polisi',
        'nomor_voyage',
        'surat_jalan_id',
        'jenis_penyesuaian',
        'jumlah_retur',
        'tipe_penyesuaian',
        'penerima',
        'total',
        'status',
        'keterangan',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'total' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    /**
     * Relationship dengan User (created_by)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship dengan User (approved_by)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship dengan SuratJalan
     */
    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id');
    }

    /**
     * Relationship dengan Pembayaran Invoice (many-to-many)
     */
    public function pembayarans()
    {
        return $this->belongsToMany(
            PembayaranInvoiceAktivitasLain::class,
            'invoice_aktivitas_lain_pembayaran',
            'invoice_aktivitas_lain_id',
            'pembayaran_invoice_aktivitas_lain_id'
        )->withPivot('jumlah_dibayar')
          ->withTimestamps();
    }
}
