<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UangJalanAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'uang_jalan_id',
        'tanggal_penyesuaian',
        'jenis_penyesuaian',
        'debit_kredit',
        'jumlah_penyesuaian',
        'alasan_penyesuaian',
        'memo',
        'created_by'
    ];

    protected $casts = [
        'tanggal_penyesuaian' => 'date',
        'jumlah_penyesuaian' => 'decimal:2'
    ];

    public function uangJalan()
    {
        return $this->belongsTo(UangJalan::class, 'uang_jalan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
