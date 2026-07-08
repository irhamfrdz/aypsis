<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoUtangSupir extends Model
{
    use HasFactory;

    protected $table = 'saldo_utang_supirs';

    protected $fillable = [
        'karyawan_id',
        'saldo',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
