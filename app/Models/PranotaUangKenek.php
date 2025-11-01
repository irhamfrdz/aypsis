<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PranotaUangKenek extends Model
{
    protected $table = 'pranota_uang_keneks';

    protected $fillable = [
        'no_pranota',
        'tanggal',
        'jumlah_surat_jalan',
        'jumlah_kenek',
        'total_uang_kenek',
        'total_hutang',
        'total_tabungan',
        'grand_total',
        'total_uang', // Keep for backward compatibility
        'keterangan',
        'status',
        'tanggal_bayar',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_bayar' => 'date',
        'approved_at' => 'datetime',
        'jumlah_surat_jalan' => 'integer',
        'jumlah_kenek' => 'integer',
        'total_uang_kenek' => 'decimal:2',
        'total_hutang' => 'decimal:2',
        'total_tabungan' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'total_uang' => 'decimal:2',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PAID => 'Paid',
            self::STATUS_CANCELLED => 'Cancelled'
        ];
    }

    public function getStatusLabelAttribute()
    {
        $statuses = self::getStatusOptions();
        return $statuses[$this->status] ?? $this->status;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->no_pranota)) {
                $model->no_pranota = $model->generateNoPranota();
            }
            
            // Auto-calculate total_uang based on grand_total
            $model->total_uang = $model->grand_total ?? 0;
        });

        static::updating(function (self $model) {
            // Auto-calculate total_uang based on grand_total
            $model->total_uang = $model->grand_total ?? 0;
        });
    }

    // Generate unique pranota number
    private function generateNoPranota()
    {
        $prefix = 'PUK'; // Pranota Uang Kenek
        $year = date('Y');
        $month = date('m');
        
        // Get last number for this month
        $lastPranota = self::where('no_pranota', 'like', "{$prefix}-{$year}{$month}%")
                          ->orderBy('no_pranota', 'desc')
                          ->first();
        
        if ($lastPranota) {
            $lastNumber = intval(substr($lastPranota->no_pranota, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . '-' . $year . $month . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function details()
    {
        return $this->hasMany(PranotaUangKenekDetail::class);
    }

    public function kenekSummary()
    {
        return $this->hasMany(PranotaUangKenekSummary::class);
    }

    public function suratJalans()
    {
        return $this->hasManyThrough(SuratJalan::class, PranotaUangKenekDetail::class, 'pranota_uang_kenek_id', 'id', 'id', 'surat_jalan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Computed attribute for uang rit (compatibility)
    public function getUangRitAttribute()
    {
        return $this->uang_rit_kenek;
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    // Status checking methods
    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSubmitted()
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    // Status update methods
    public function submit()
    {
        $this->update([
            'status' => self::STATUS_SUBMITTED,
            'updated_by' => Auth::id()
        ]);
    }

    public function approve()
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'updated_by' => Auth::id()
        ]);
    }

    public function markAsPaid($tanggalBayar = null)
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'tanggal_bayar' => $tanggalBayar ?? now()->toDateString(),
            'updated_by' => Auth::id()
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'updated_by' => Auth::id()
        ]);
    }

    // Payment relationships (for future implementation)
    public function pembayaranUangRits()
    {
        return $this->belongsToMany(PembayaranUangRit::class, 'pembayaran_pranota_uang_kenek')
            ->withPivot(['uang_kenek_dibayar'])
            ->withTimestamps();
    }

    // Calculate total paid amount
    public function getTotalPaidAttribute()
    {
        return $this->pembayaranUangRits()
            ->sum('pembayaran_pranota_uang_kenek.uang_kenek_dibayar');
    }
}
