<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaObAntarGudang extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_pranota_ob_antar_gudangs';

    protected $fillable = [
        'nomor_pembayaran',
        'nomor_accurate',
        'nomor_cetakan',
        'bank',
        'jenis_transaksi',
        'tanggal_kas',
        'total_pembayaran',
        'penyesuaian',
        'total_setelah_penyesuaian',
        'alasan_penyesuaian',
        'keterangan',
        'status',
        'pranota_ob_antar_gudang_ids',
        'akun_coa_id',
        'akun_bank_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_kas' => 'date',
        'total_pembayaran' => 'decimal:2',
        'penyesuaian' => 'decimal:2',
        'total_setelah_penyesuaian' => 'decimal:2',
        'pranota_ob_antar_gudang_ids' => 'array',
    ];

    /**
     * Get the pranota OB Antar Gudangs associated with this payment
     * This is an accessor method, not a relationship
     */
    public function getPranotaObAntarGudangsAttribute()
    {
        $ids = $this->pranota_ob_antar_gudang_ids ?? [];

        // Ensure it's an array
        if (is_string($ids)) {
            $ids = json_decode($ids, true) ?? [];
        }

        if (empty($ids) || ! is_array($ids)) {
            return collect([]);
        }

        return PranotaObAntarGudang::whereIn('id', $ids)->get();
    }

    /**
     * Get the user who created this payment
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this payment
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the bank COA associated with this payment
     */
    public function akunBank()
    {
        return $this->belongsTo(Coa::class, 'akun_bank_id');
    }

    /**
     * Get the expense COA associated with this payment
     */
    public function akunCoa()
    {
        return $this->belongsTo(Coa::class, 'akun_coa_id');
    }

    /**
     * Generate nomor pembayaran
     */
    public static function generateNomorPembayaran()
    {
        $prefix = 'PY-OB-AG-';
        $date = date('Ym');

        // Get the last number for this month
        $lastNumber = static::where('nomor_pembayaran', 'like', $prefix.$date.'%')
            ->orderBy('nomor_pembayaran', 'desc')
            ->value('nomor_pembayaran');

        if ($lastNumber) {
            // Extract the last 4 digits and increment
            $lastSequence = intval(substr($lastNumber, -4));
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return $prefix.$date.'-'.str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_pembayaran)) {
                $model->nomor_pembayaran = static::generateNomorPembayaran();
            }
            if (empty($model->created_by)) {
                $model->created_by = auth()->id();
            }
            $model->updated_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }
}
