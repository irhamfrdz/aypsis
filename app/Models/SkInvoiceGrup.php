<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class SkInvoiceGrup extends Model
{
    use Auditable;

    protected $table = 'sk_invoice_grups';

    protected $fillable = [
        'nomor_invoice',
        'vendor_id',
        'tanggal_invoice',
        'status_pembayaran',
        'deskripsi',
        'adjustment_biaya',
        'adjustment_keterangan',
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'adjustment_biaya' => 'integer',
    ];

    public function vendor()
    {
        return $this->belongsTo(VendorKontainerSewa::class, 'vendor_id');
    }

    public function tagihans()
    {
        return $this->belongsToMany(SkTagihanBulan::class, 'sk_invoice_grup_tagihans', 'invoice_grup_id', 'tagihan_bulan_id');
    }
}
