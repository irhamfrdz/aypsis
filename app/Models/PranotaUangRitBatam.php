<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PranotaUangRitBatam extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pranota_uang_rit_batams';

    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'supir_nama',
        'total_rit',
        'penyesuaian',
        'total_amount',
        'status_pembayaran',
        'catatan',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
        'total_rit' => 'decimal:2',
        'penyesuaian' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    const STATUS_UNPAID = 'unpaid';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    public function items()
    {
        return $this->hasMany(PranotaUangRitBatamItem::class, 'pranota_uang_rit_batam_id');
    }

    public function suratJalanBatams()
    {
        return $this->belongsToMany(SuratJalanBatam::class, 'pranota_uang_rit_batam_items', 'pranota_uang_rit_batam_id', 'surat_jalan_batam_id')
                    ->withPivot('uang_rit')
                    ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function generateNomorPranota()
    {
        $date = now()->format('Ymd');
        $prefix = 'PRN-URBTM-' . $date . '-';
        
        $lastRecord = static::withTrashed()->where('nomor_pranota', 'LIKE', $prefix . '%')
                           ->orderBy('nomor_pranota', 'desc')
                           ->first();
        
        $runningNumber = 1;
        
        if ($lastRecord) {
            $lastNumber = str_replace($prefix, '', $lastRecord->nomor_pranota);
            $runningNumber = intval($lastNumber) + 1;
        }
        
        return $prefix . str_pad($runningNumber, 4, '0', STR_PAD_LEFT);
    }
}
