<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class PembayaranAktivitasLainnya extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'pembayaran_aktivitas_lainnya';

    protected $fillable = [
        'nomor_pembayaran',
        'nomor_accurate',
        'tanggal_pembayaran',
        'nomor_voyage',
        'nama_kapal',
        'total_pembayaran',
        'aktivitas_pembayaran',
        'plat_nomor',
        'pilih_bank',
        'akun_biaya_id',
        'jenis_transaksi',
        'status',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'total_pembayaran' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    /**
     * Relationship dengan items pembayaran
     */
    public function items()
    {
        return $this->hasMany(PembayaranAktivitasLainnyaItem::class, 'pembayaran_id');
    }

    /**
     * Relationship dengan user yang membuat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship dengan user yang approve
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship dengan akun bank
     */
    public function akunBank()
    {
        return $this->belongsTo(Coa::class, 'pilih_bank');
    }

    /**
     * Relationship dengan akun biaya
     */
    public function akunBiaya()
    {
        return $this->belongsTo(Coa::class, 'akun_biaya_id');
    }

    /**
     * Relationship dengan CoaTransaction
     */
    public function coaTransactions()
    {
        return $this->hasMany(CoaTransaction::class, 'nomor_referensi', 'nomor_pembayaran');
    }

    /**
     * Generate nomor pembayaran otomatis
     */
    public static function generateNomor()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "PMS{$year}{$month}";

        $lastRecord = self::where('nomor_pembayaran', 'like', $prefix . '%')
            ->orderBy('nomor_pembayaran', 'desc')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->nomor_pembayaran, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_pembayaran', [$startDate, $endDate]);
    }

    /**
     * Scope untuk filter berdasarkan voyage
     */
    public function scopeByVoyage($query, $voyage)
    {
        return $query->where('nomor_voyage', 'like', "%{$voyage}%");
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'pending' => 'yellow',
            'approved' => 'blue',
            'rejected' => 'red',
            'paid' => 'green',
            default => 'gray'
        };
    }

    /**
     * Get jenis transaksi label
     */
    public function getJenisTransaksiLabelAttribute()
    {
        return $this->jenis_transaksi === 'debit' ? 'Pemasukan' : 'Pengeluaran';
    }
}