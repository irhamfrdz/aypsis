<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PranotaLemburKaryawanHeader extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pranota_lembur_karyawan_headers';

    protected $fillable = [
        'nomor_pranota',
        'nomor_cetakan',
        'tanggal_pranota',
        'total_biaya',
        'adjustment',
        'total_setelah_adjustment',
        'status',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'pranota_puml_id',
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
        'total_biaya' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'total_setelah_adjustment' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function karyawans()
    {
        return $this->hasMany(PranotaLemburKaryawan::class, 'pranota_lembur_karyawan_header_id');
    }
}
