<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContainerTrip extends Model
{
    protected $fillable = [
        'vendor_id',
        'no_kontainer',
        'ukuran',
        'tgl_ambil',
        'tgl_kembali',
        'harga_sewa',
    ];

    protected $casts = [
        'tgl_ambil' => 'date',
        'tgl_kembali' => 'date',
        'harga_sewa' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
