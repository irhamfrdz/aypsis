<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorInvoice extends Model
{
    protected $fillable = [
        'vendor_id',
        'no_invoice',
        'tgl_invoice',
        'total_dpp',
        'total_ppn',
        'total_pph23',
        'total_materai',
        'total_netto',
    ];

    protected $casts = [
        'tgl_invoice' => 'date',
        'total_dpp' => 'decimal:2',
        'total_ppn' => 'decimal:2',
        'total_pph23' => 'decimal:2',
        'total_materai' => 'decimal:2',
        'total_netto' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(VendorKontainerSewa::class, 'vendor_id');
    }
}
