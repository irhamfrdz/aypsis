<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaKontainer extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_pranota_kontainer';

    protected $fillable = [
        'nomor_pembayaran',
        'bank',
        'jenis_transaksi',
        'tanggal_kas',
        'tanggal_pembayaran',
        'total_pembayaran',
        'total_tagihan_penyesuaian',
        'total_tagihan_setelah_penyesuaian',
        'alasan_penyesuaian',
        'keterangan',
        'status',
        'dibuat_oleh',
        'disetujui_oleh',
        'tanggal_persetujuan'
    ];

    protected $casts = [
        'tanggal_kas' => 'date',
        'tanggal_pembayaran' => 'date',
        'tanggal_persetujuan' => 'datetime',
        'total_pembayaran' => 'decimal:2',
        'penyesuaian' => 'decimal:2',
        'total_setelah_penyesuaian' => 'decimal:2'
    ];

    /**
     * Relationship to payment items
     */
    public function items()
    {
        return $this->hasMany(PembayaranPranotaKontainerItem::class);
    }

    /**
     * Relationship to pranota through items
     */
    public function pranotas()
    {
        return $this->belongsToMany(
            Pranota::class,
            'pembayaran_pranota_kontainer_items',
            'pembayaran_pranota_kontainer_id',
            'pranota_id'
        )->withPivot('amount', 'keterangan')->withTimestamps();
    }

    /**
     * Relationship to user who created the payment
     */
    public function pembuatPembayaran()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Relationship to user who approved the payment
     */
    public function penyetujuPembayaran()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get status text for display
     */
    public function getStatusText()
    {
        return match($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Generate nomor pembayaran otomatis
     */
    public static function generateNomorPembayaran($nomorCetakan = 1)
    {
        $today = now();
        $prefix = 'BPK'; // Bayar Pranota Kontainer
        $tahun = $today->format('y');
        $bulan = $today->format('m');

        // Count payments created today
        $count = self::whereDate('created_at', $today->toDateString())->count();
        $sequence = str_pad($count + 1, 6, '0', STR_PAD_LEFT);

        return "{$prefix}-{$nomorCetakan}-{$tahun}-{$bulan}-{$sequence}";
    }
}
