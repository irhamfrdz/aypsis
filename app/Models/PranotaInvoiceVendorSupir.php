<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaInvoiceVendorSupir extends Model
{
    protected $fillable = [
        'no_pranota',
        'vendor_id',
        'tanggal_pranota',
        'total_nominal',
        'pph',
        'uang_muat',
        'total_uang_muat',
        'grand_total',
        'status_pembayaran',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(VendorSupir::class, 'vendor_id');
    }

    public function invoiceTagihanVendors()
    {
        return $this->hasMany(InvoiceTagihanVendor::class, 'pranota_invoice_vendor_supir_id');
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
