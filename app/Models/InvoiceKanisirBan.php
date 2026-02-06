<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceKanisirBan extends Model
{
    protected $fillable = [
        'nomor_invoice',
        'nomor_faktur',
        'tanggal_invoice',
        'vendor',
        'total_biaya',
        'jumlah_ban',
        'keterangan',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceKanisirBanItem::class);
    }
}
