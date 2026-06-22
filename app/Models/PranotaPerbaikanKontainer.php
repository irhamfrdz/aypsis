<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PranotaPerbaikanKontainer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pranota_perbaikan_kontainers';

    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'vendor',
        'bank',
        'rekening',
        'penerima',
        'total_biaya',
        'adjustment',
        'keterangan',
        'items',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'items' => 'array',
        'tanggal_pranota' => 'date',
        'total_biaya' => 'decimal:2',
        'adjustment' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function calculateTotalCatAmount()
    {
        if (!is_array($this->items)) {
            return 0;
        }
        $total = 0;
        foreach ($this->items as $item) {
            $total += floatval($item['biaya_cat'] ?? 0);
        }
        return $total;
    }
}
