<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class PranotaLembur extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'pranota_lemburs';

    protected $fillable = [
        'nomor_pranota',
        'nomor_cetakan',
        'tanggal_pranota',
        'total_biaya',
        'adjustment',
        'alasan_adjustment',
        'total_setelah_adjustment',
        'catatan',
        'status',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
        'total_biaya' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'total_setelah_adjustment' => 'decimal:2',
        'approved_at' => 'datetime',
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
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    // Relationships
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

    public function suratJalans()
    {
        return $this->belongsToMany(SuratJalan::class, 'pranota_lembur_surat_jalan', 'pranota_lembur_id', 'surat_jalan_id')
            ->withPivot('supir', 'no_plat', 'is_lembur', 'is_nginap', 'biaya_lembur', 'biaya_nginap', 'total_biaya')
            ->withTimestamps();
    }

    public function suratJalanBongkarans()
    {
        return $this->belongsToMany(SuratJalanBongkaran::class, 'pranota_lembur_surat_jalan', 'pranota_lembur_id', 'surat_jalan_bongkaran_id')
            ->withPivot('supir', 'no_plat', 'is_lembur', 'is_nginap', 'biaya_lembur', 'biaya_nginap', 'total_biaya')
            ->withTimestamps();
    }

    // Helper methods
    public function getStatusLabelAttribute()
    {
        $statuses = self::getStatusOptions();
        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_DRAFT => 'bg-gray-100 text-gray-800',
            self::STATUS_SUBMITTED => 'bg-blue-100 text-blue-800',
            self::STATUS_APPROVED => 'bg-green-100 text-green-800',
            self::STATUS_PAID => 'bg-purple-100 text-purple-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getFormattedTotalBiayaAttribute()
    {
        return 'Rp ' . number_format($this->total_biaya, 0, ',', '.');
    }

    public function getFormattedTotalSetelahAdjustmentAttribute()
    {
        return 'Rp ' . number_format($this->total_setelah_adjustment, 0, ',', '.');
    }
}
