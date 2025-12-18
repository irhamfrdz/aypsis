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
}
