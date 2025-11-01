<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Auditable;

class PranotaUangRit extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'no_pranota',
        'tanggal',
        'surat_jalan_id',
        'no_surat_jalan',
        'supir_nama',
        'kenek_nama',
        'uang_rit_supir',
        'total_rit',
        'total_uang',
        'total_hutang',
        'total_tabungan',
        'grand_total_bersih',
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
        'uang_rit_supir' => 'decimal:2',
        'total_rit' => 'decimal:2',
        'total_uang' => 'decimal:2',
        'total_hutang' => 'decimal:2',
        'total_tabungan' => 'decimal:2',
        'grand_total_bersih' => 'decimal:2',
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

    // Relationships
    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the supir details for this pranota
     */
    public function supirDetails()
    {
        return $this->hasMany(PranotaUangRitSupirDetail::class, 'no_pranota', 'no_pranota');
    }

    /**
     * Get all pranota records with the same no_pranota (grouped records)
     */
    public function groupedPranota()
    {
        return $this->hasMany(PranotaUangRit::class, 'no_pranota', 'no_pranota');
    }

    // Boot method for auto-generating no_pranota
    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->no_pranota)) {
                $model->no_pranota = $model->generateNoPranota();
            }
            
            // Auto-calculate total_uang (only uang_rit now)
            $model->total_uang = $model->uang_rit;
        });

        static::updating(function (self $model) {
            // Auto-calculate total_uang (only uang_rit now)
            $model->total_uang = $model->uang_rit;
        });
    }

    // Generate unique pranota number
    private function generateNoPranota()
    {
        $prefix = 'PUR'; // Pranota Uang Rit
        $year = date('Y');
        $month = date('m');
        
        // Get the last number for this month
        $lastPranota = self::where('no_pranota', 'like', $prefix . $year . $month . '%')
            ->orderBy('no_pranota', 'desc')
            ->first();
        
        if ($lastPranota) {
            $lastNumber = (int) substr($lastPranota->no_pranota, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
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

    public function scopeBySupir($query, $supirNama)
    {
        return $query->where('supir_nama', 'like', '%' . $supirNama . '%');
    }

    /**
     * Pembayaran uang rit
     */
    public function pembayaranUangRits(): BelongsToMany
    {
        return $this->belongsToMany(PembayaranUangRit::class, 'pembayaran_pranota_uang_rit')
            ->withPivot(['uang_jalan_dibayar', 'uang_rit_dibayar'])
            ->withTimestamps();
    }

    /**
     * Check if this pranota is already paid
     */
    public function getIsPaidAttribute()
    {
        return $this->pembayaranUangRits()
            ->where('status', PembayaranUangRit::STATUS_PAID)
            ->exists();
    }

    /**
     * Get total paid amount
     */
    public function getTotalPaidAttribute()
    {
        return $this->pembayaranUangRits()
            ->where('status', PembayaranUangRit::STATUS_PAID)
            ->sum('pembayaran_pranota_uang_rit.uang_jalan_dibayar') +
            $this->pembayaranUangRits()
            ->where('status', PembayaranUangRit::STATUS_PAID)
            ->sum('pembayaran_pranota_uang_rit.uang_rit_dibayar');
    }
}
