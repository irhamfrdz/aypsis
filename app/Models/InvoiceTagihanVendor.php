<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceTagihanVendor extends Model
{
    protected $fillable = [
        'no_invoice',
        'vendor_id',
        'tanggal_invoice',
        'total_nominal',
        'status_pembayaran',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(VendorSupir::class, 'vendor_id');
    }

    public function tagihanSupirVendors()
    {
        return $this->hasMany(TagihanSupirVendor::class, 'invoice_tagihan_vendor_id');
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
