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
        'jumlah_mel',
        'jumlah_pelancar',
        'jumlah_kawalan',
        'jumlah_parkir',
        'alasan_penyesuaian',
        'memo',
        'created_by'
    ];

    protected $casts = [
        'tanggal_penyesuaian' => 'date',
        'jumlah_penyesuaian' => 'decimal:2',
        'jumlah_mel' => 'decimal:2',
        'jumlah_pelancar' => 'decimal:2',
        'jumlah_kawalan' => 'decimal:2',
        'jumlah_parkir' => 'decimal:2'
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
