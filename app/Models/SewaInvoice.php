<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SewaInvoice extends Model
{
    protected $table = 'sewa_invoices';

    protected $primaryKey = 'nomor_invoice';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nomor_invoice',
        'id_customer',
        'tanggal_invoice',
        'status_pembayaran',
        'deskripsi',
        'adjustment_biaya',
        'adjustment_keterangan',
    ];

    public function customer()
    {
        return $this->belongsTo(SewaCustomer::class, 'id_customer', 'id_customer');
    }

    public function tagihans()
    {
        return $this->hasMany(SewaTagihan::class, 'nomor_invoice_grup', 'nomor_invoice');
    }
}
