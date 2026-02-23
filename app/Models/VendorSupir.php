<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class VendorSupir extends Model
{
    use Auditable;

    protected $table = 'vendor_supirs';

    protected $fillable = [
        'nama_vendor',
        'no_hp',
        'alamat',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
