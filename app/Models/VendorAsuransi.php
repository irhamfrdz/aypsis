<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAsuransi extends Model
{
    use HasFactory;

    protected $table = 'vendor_asuransi';

    protected $fillable = [
        'kode',
        'nama_asuransi',
        'alamat',
        'telepon',
        'email',
        'keterangan',
        'catatan',
        'created_by',
        'updated_by'
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
