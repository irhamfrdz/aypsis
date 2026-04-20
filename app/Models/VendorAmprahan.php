<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorAmprahan extends Model
{
    use Auditable;

    protected $table = 'vendor_amprahans';

    protected $fillable = [
        'nama_toko',
        'alamat_toko',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
