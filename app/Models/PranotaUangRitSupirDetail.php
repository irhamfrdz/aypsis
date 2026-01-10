<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PranotaUangRitSupirDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_pranota',
        'supir_nama',
        'jumlah_rit',
        'total_uang_supir',
        'hutang',
        'tabungan',
        'bpjs',
        'grand_total',
    ];

    protected $casts = [
        'jumlah_rit' => 'integer',
        'total_uang_supir' => 'decimal:2',
        'hutang' => 'decimal:2',
        'tabungan' => 'decimal:2',
        'bpjs' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    /**
     * Calculate grand total automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->grand_total = $model->total_uang_supir - $model->hutang - $model->tabungan - $model->bpjs;
        });
    }

    /**
     * Get the computed grand total attribute
     */
    public function getGrandTotalAttribute($value)
    {
        // If the value is already stored, return it
        if ($value !== null) {
            return $value;
        }
        
        // Otherwise, calculate it
        return $this->total_uang_supir - $this->hutang - $this->tabungan - $this->bpjs;
    }

    /**
     * Get the pranota uang rit records for this detail
     */
    public function pranotaUangRits()
    {
        return $this->hasMany(PranotaUangRit::class, 'no_pranota', 'no_pranota')
                    ->where('supir_nama', $this->supir_nama);
    }
}
