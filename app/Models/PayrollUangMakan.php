<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollUangMakan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'periode_start' => 'date',
        'periode_end' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
