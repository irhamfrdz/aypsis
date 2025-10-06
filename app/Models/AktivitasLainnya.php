<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AktivitasLainnya extends Model
{
    use HasFactory;

    protected $table = 'aktivitas_lainnya';

    protected $fillable = [
        'nomor_aktivitas',
        'tanggal_aktivitas',
        'deskripsi_aktivitas',
        'kategori',
        'vendor_id',
        'nominal',
        'status',
        'keterangan',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'tanggal_aktivitas' => 'date',
        'nominal' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    /**
     * Relationship dengan User (created_by)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Alias for creator
     */
    public function createdBy()
    {
        return $this->creator();
    }

    /**
     * Relationship dengan User (approved_by)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Alias for approver
     */
    public function approvedBy()
    {
        return $this->approver();
    }

    /**
     * Relationship dengan VendorBengkel (jika diperlukan)
     */
    public function vendor()
    {
        return $this->belongsTo(VendorBengkel::class, 'vendor_id');
    }

    /**
     * Relationship dengan pembayaran (many-to-many)
     */
    public function pembayaran()
    {
        return $this->belongsToMany(PembayaranAktivitasLainnya::class, 'pembayaran_aktivitas_lainnya_items', 'aktivitas_id', 'pembayaran_id')
                    ->withPivot('nominal_dibayar', 'keterangan')
                    ->withTimestamps();
    }

    /**
     * Status options
     */
    public static function getStatusOptions()
    {
        return [
            'draft' => 'Draft',
            'pending' => 'Pending Approval',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'paid' => 'Sudah Dibayar'
        ];
    }

    /**
     * Kategori options
     */
    public static function getKategoriOptions()
    {
        return [
            'operasional' => 'Operasional',
            'maintenance' => 'Maintenance',
            'administrasi' => 'Administrasi',
            'transport' => 'Transport',
            'lainnya' => 'Lainnya'
        ];
    }

    /**
     * Generate nomor aktivitas otomatis
     */
    public static function generateNomorAktivitas()
    {
        $date = now();
        $prefix = 'AL/' . $date->format('Y/m') . '/';

        $lastRecord = self::where('nomor_aktivitas', 'like', $prefix . '%')
            ->orderBy('nomor_aktivitas', 'desc')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->nomor_aktivitas, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Check if aktivitas has payment pending
     */
    public function hasPaymentPending()
    {
        return $this->pembayaran()->where('pembayaran_aktivitas_lainnya.status', 'pending')->exists();
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus()
    {
        if ($this->pembayaran()->where('pembayaran_aktivitas_lainnya.status', 'paid')->exists()) {
            return 'Sudah Dibayar';
        }

        if ($this->pembayaran()->where('pembayaran_aktivitas_lainnya.status', 'approved')->exists()) {
            return 'Siap Dibayar';
        }

        if ($this->hasPaymentPending()) {
            return 'Pending Payment';
        }

        return 'Belum Dibayar';
    }

    /**
     * Get payment status color
     */
    public function getPaymentStatusColor()
    {
        switch ($this->getPaymentStatus()) {
            case 'Sudah Dibayar':
                return 'text-green-600 bg-green-50';
            case 'Pending Payment':
                return 'text-yellow-600 bg-yellow-50';
            default:
                return 'text-red-600 bg-red-50';
        }
    }

    /**
     * Scope untuk filter status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter kategori
     */
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope untuk aktivitas yang bisa dibayar
     */
    public function scopePayable($query)
    {
        return $query->where('status', 'approved')
                    ->whereDoesntHave('pembayaran', function($q) {
                        $q->where('pembayaran_aktivitas_lainnya.status', 'paid');
                    });
    }
}
