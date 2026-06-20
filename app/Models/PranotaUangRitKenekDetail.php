<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaUangRitKenekDetail extends Model
{
    protected $fillable = [
        'no_pranota',
        'kenek_nama',
        'total_uang_kenek',
        'hutang',
        'tabungan',
        'bpjs',
        'grand_total',
    ];

    protected $casts = [
        'total_uang_kenek' => 'decimal:2',
        'hutang' => 'decimal:2',
        'tabungan' => 'decimal:2',
        'bpjs' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    /**
     * Boot method to auto-calculate grand_total
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Calculate grand total: uang_kenek - hutang - tabungan - bpjs
            $model->grand_total = $model->total_uang_kenek - $model->hutang - $model->tabungan - $model->bpjs;
        });
    }

    /**
     * Get the pranota uang rit kenek that owns this detail
     */
    public function pranotaUangRitKenek()
    {
        return $this->belongsTo(PranotaUangRitKenek::class, 'no_pranota', 'no_pranota');
    }

    /**
     * Get the karyawan (kenek) relation based on kenek_nama
     */
    public function kenekKaryawan()
    {
        return $this->belongsTo(Karyawan::class, 'kenek_nama', 'nama_lengkap');
    }

    /**
     * Get karyawan data with fallback logic
     * Try nama_lengkap first, then nama_panggilan
     */
    public function getKenekKaryawanDataAttribute()
    {
        // Try relation first (nama_lengkap match)
        if ($this->kenekKaryawan) {
            return $this->kenekKaryawan;
        }

        // Fallback 1: exact match on active employee
        $exactActive = Karyawan::whereNull('tanggal_berhenti')
            ->where(function ($q) {
                $q->where('nama_panggilan', $this->kenek_nama)
                    ->orWhere('nama_lengkap', $this->kenek_nama);
            })->first();
        if ($exactActive) {
            return $exactActive;
        }

        // Fallback 2: loose match on active employee
        $looseActive = Karyawan::whereNull('tanggal_berhenti')
            ->where('nama_lengkap', 'LIKE', '%'.$this->kenek_nama.'%')
            ->first();
        if ($looseActive) {
            return $looseActive;
        }

        // Fallback 3: exact match on any employee (including stopped/inactive ones)
        $exact = Karyawan::where('nama_panggilan', $this->kenek_nama)
            ->orWhere('nama_lengkap', $this->kenek_nama)
            ->first();
        if ($exact) {
            return $exact;
        }

        // Fallback 4: loose search on any employee
        return Karyawan::where('nama_lengkap', 'LIKE', '%'.$this->kenek_nama.'%')->first();
    }
}
