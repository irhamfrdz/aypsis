<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PranotaBiayaBensin extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'total_biaya',
        'status',
        'catatan',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
        'total_biaya' => 'decimal:2',
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

    public function biayaBensins()
    {
        return $this->hasMany(BiayaBensin::class, 'pranota_biaya_bensin_id');
    }

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
}
