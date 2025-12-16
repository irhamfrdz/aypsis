<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PembayaranPranotaOb extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_pranota_obs';

    protected $fillable = [
        'nomor_pembayaran',
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
        'pranota_ob_ids',
        'pembayaran_ob_id',
        'kapal',
        'voyage',
        'dp_amount',
        'total_biaya_pranota',
        'breakdown_supir',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal_kas' => 'date',
        'total_pembayaran' => 'decimal:2',
        'penyesuaian' => 'decimal:2',
        'total_setelah_penyesuaian' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'total_biaya_pranota' => 'decimal:2',
        'pranota_ob_ids' => 'array',
        'breakdown_supir' => 'array'
    ];

    /**
     * Get the pranota OBs associated with this payment
     */
    public function pranotaObs()
    {
        $ids = $this->pranota_ob_ids ?? [];
        if (empty($ids)) {
            return collect([]);
        }
        return PranotaOb::whereIn('id', $ids)->get();
    }

    /**
     * Get the DP payment (PembayaranOb) associated with this payment
     */
    public function pembayaranOb()
    {
        return $this->belongsTo(PembayaranOb::class, 'pembayaran_ob_id');
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
     * Generate nomor pembayaran
     */
    public static function generateNomorPembayaran()
    {
        $prefix = 'PY-OB-';
        $date = date('Ym');
        
        // Get the last number for this month
        $lastNumber = static::where('nomor_pembayaran', 'like', $prefix . $date . '%')
            ->orderBy('nomor_pembayaran', 'desc')
            ->value('nomor_pembayaran');
        
        if ($lastNumber) {
            // Extract the last 4 digits and increment
            $lastSequence = intval(substr($lastNumber, -4));
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }
        
        return $prefix . $date . '-' . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
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
