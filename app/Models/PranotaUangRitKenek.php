<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class PranotaUangRitKenek extends Model
{
    use HasFactory, Auditable;

    protected $table = 'pranota_uang_rit_keneks';

    protected $fillable = [
        'no_pranota',
        'tanggal',
        'surat_jalan_id',
        'surat_jalan_bongkaran_id',
        'no_surat_jalan',
        'kenek_nama',
        'no_plat',
        'uang_jalan',
        'uang_rit',
        'uang_rit_kenek',
        'total_rit',
        'total_uang',
        'total_hutang',
        'total_tabungan',
        'total_bpjs',
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
        'uang_jalan' => 'decimal:2',
        'uang_rit' => 'decimal:2',
        'uang_rit_kenek' => 'decimal:2',
        'total_rit' => 'decimal:2',
        'total_uang' => 'decimal:2',
        'total_hutang' => 'decimal:2',
        'total_tabungan' => 'decimal:2',
        'total_bpjs' => 'decimal:2',
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

    public function suratJalanBongkaran()
    {
        return $this->belongsTo(SuratJalanBongkaran::class);
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

    // Has many kenek details
    public function kenekDetails()
    {
        return $this->hasMany(PranotaUangRitKenekDetail::class, 'no_pranota', 'no_pranota');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    // Helper methods
    public function canEdit()
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SUBMITTED]);
    }

    public function canDelete()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canSubmit()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canApprove()
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function canMarkAsPaid()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    // Generate nomor pranota
    public static function generateNoPranota()
    {
        $prefix = 'PNK'; // Pranota Kenek
        $date = now()->format('Ymd');
        
        // Get last pranota number for today
        $lastPranota = self::where('no_pranota', 'like', "{$prefix}-{$date}-%")
            ->orderBy('no_pranota', 'desc')
            ->first();

        if ($lastPranota) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($lastPranota->no_pranota, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $date, $newSequence);
    }
}
