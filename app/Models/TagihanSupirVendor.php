<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanSupirVendor extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }

    public function vendor()
    {
        return $this->belongsTo(VendorSupir::class, 'vendor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function invoice()
    {
        return $this->belongsTo(InvoiceTagihanVendor::class, 'invoice_tagihan_vendor_id');
    }
}
