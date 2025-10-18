<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class PembayaranPranotaKontainer extends Model
{
    use HasFactory;

    use Auditable;
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
        'tanggal_persetujuan',
        'dp_payment_id',
        'dp_amount'
    ];

    protected $casts = [
        'tanggal_kas' => 'date',
        'tanggal_pembayaran' => 'date',
        'tanggal_persetujuan' => 'datetime',
        'total_pembayaran' => 'decimal:2',
        'penyesuaian' => 'decimal:2',
        'total_setelah_penyesuaian' => 'decimal:2',
        'dp_amount' => 'decimal:2'
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
     * Relationship to DP payment from pembayaran aktivitas lainnya
     */
    public function dpPayment()
    {
        return $this->belongsTo(\App\Models\PembayaranAktivitasLainnya::class, 'dp_payment_id');
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
     * Generate nomor pembayaran untuk preview (tanpa update ke database)
     *
     * Format: [kode bank]-[1 digit cetakan]-[2 digit tahun]-[2 digit bulan]-[6 digit nomor terakhir]
     * Example: TST-1-25-09-000001 (untuk Kas Kecil)
     */
    public static function generateNomorPembayaran($nomorCetakan = 1, $kodeBank = '000')
    {
        $today = now();
        $prefix = $kodeBank ?: '000'; // Gunakan kode bank atau default 000
        $tahun = $today->format('y'); // 2 digit year
        $bulan = $today->format('m'); // 2 digit month

        // Get next nomor pembayaran from master nomor terakhir dengan modul nomor_pembayaran
        $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'nomor_pembayaran')->lockForUpdate()->first();
        if (!$nomorTerakhir) {
            throw new \Exception('Modul nomor_pembayaran tidak ditemukan di master nomor terakhir.');
        }

        $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
        $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return "{$prefix}-{$nomorCetakan}-{$tahun}-{$bulan}-{$sequence}";
    }

    /**
     * Generate nomor pembayaran dan update nomor terakhir di database
     * Hanya digunakan saat pembayaran berhasil disimpan
     */
    public static function generateAndUpdateNomorPembayaran($nomorCetakan = 1, $kodeBank = '000')
    {
        $today = now();
        $prefix = $kodeBank ?: '000'; // Gunakan kode bank atau default 000
        $tahun = $today->format('y'); // 2 digit year
        $bulan = $today->format('m'); // 2 digit month

        // Get and update nomor terakhir from master nomor terakhir dengan modul nomor_pembayaran
        $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'nomor_pembayaran')->lockForUpdate()->first();
        if (!$nomorTerakhir) {
            throw new \Exception('Modul nomor_pembayaran tidak ditemukan di master nomor terakhir.');
        }

        $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
        $nomorTerakhir->nomor_terakhir = $nextNumber;
        $nomorTerakhir->save();

        $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return "{$prefix}-{$nomorCetakan}-{$tahun}-{$bulan}-{$sequence}";
    }
}
