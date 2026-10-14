<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'nomor_pembayaran',
        'tanggal_pembayaran',
        'total_pembayaran',
        'metode_pembayaran',
        'catatan_pembayaran',
    ];

}
