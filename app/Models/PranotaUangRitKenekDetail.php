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
        'grand_total'
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
     * Get the pranota uang rit that owns this detail
     */
    public function pranotaUangRit()
    {
        return $this->belongsTo(PranotaUangRit::class, 'no_pranota', 'no_pranota');
    }
}
