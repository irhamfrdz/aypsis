<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianBbmBatam extends Model
{
    use Auditable, HasFactory;

    protected $table = 'pembelian_bbm_batams';

    protected $fillable = [
        'nomor_bukti',
        'tanggal',
        'jumlah_liter',
        'harga_per_liter',
        'total_harga',
        'supplier',
        'nomor_nota',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_liter' => 'decimal:2',
        'harga_per_liter' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Generate nomor bukti otomatis (BBMB-YYYYMM-001)
     */
    public static function generateNextInvoice()
    {
        $yearMonth = date('Ym');
        $prefix = 'BBMB-'.$yearMonth.'-';

        $lastRecord = self::where('nomor_bukti', 'like', $prefix.'%')
            ->orderBy('nomor_bukti', 'desc')
            ->first();

        if (! $lastRecord) {
            return $prefix.'001';
        }

        $lastNumber = intval(substr($lastRecord->nomor_bukti, -3));
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return $prefix.$nextNumber;
    }
}
