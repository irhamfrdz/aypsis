<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class PembayaranUangRit extends Model
{
    protected $fillable = [
        'no_pembayaran',
        'tanggal_pembayaran',
        'nama_supir',
        'no_plat',
        'total_uang_jalan',
        'total_uang_rit',
        'total_pembayaran',
        'keterangan',
        'status',
        'created_by',
        'paid_by',
        'paid_at',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'paid_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PAID = 'paid';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_pembayaran)) {
                $model->no_pembayaran = static::generateNoPembayaran();
            }
            
            if (empty($model->created_by)) {
                $model->created_by = Auth::id();
            }
        });
    }

    /**
     * Generate unique payment number
     */
    public static function generateNoPembayaran()
    {
        $date = now()->format('Ymd');
        $prefix = "PAY-{$date}-";
        
        $lastNumber = static::where('no_pembayaran', 'like', "{$prefix}%")
            ->orderBy('no_pembayaran', 'desc')
            ->first();

        if ($lastNumber) {
            $lastSequence = intval(substr($lastNumber->no_pembayaran, -4));
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return $prefix . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Pranota Uang Rit yang dibayar
     */
    public function pranotaUangRits(): BelongsToMany
    {
        return $this->belongsToMany(PranotaUangRit::class, 'pembayaran_pranota_uang_rit')
            ->withPivot(['uang_jalan_dibayar', 'uang_rit_dibayar'])
            ->withTimestamps();
    }

    /**
     * User yang membuat pembayaran
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User yang menandai sebagai dibayar
     */
    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PAID => 'Dibayar',
            default => 'Unknown'
        };
    }

    /**
     * Mark as paid
     */
    public function markAsPaid()
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'paid_by' => Auth::id(),
            'paid_at' => now(),
        ]);
    }
}
