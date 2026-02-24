<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPricelistVendorSupir extends Model
{
    protected $fillable = [
        'vendor_id',
        'dari',
        'ke',
        'jenis_kontainer',
        'nominal',
        'status',
        'keterangan',
        'created_by',
        'updated_by',
    ];

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
}
