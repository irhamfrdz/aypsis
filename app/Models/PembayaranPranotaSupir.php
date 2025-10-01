<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaSupir extends Model
{
    protected $table = 'pembayaran_pranota_supir';
    protected $guarded = [];

    protected $casts = [
        'tanggal_kas' => 'date',
        'tanggal_pembayaran' => 'date',
    ];

    public function pranotas()
    {
        return $this->belongsToMany(PranotaSupir::class, 'pembayaran_pranota_supir_pranota_supir', 'pembayaran_pranota_supir_id', 'pranota_supir_id');
    }

    /**
     * Generate nomor pembayaran berdasarkan master nomor terakhir dengan modul nomor_pembayaran
     * Format: [kode_bank]-[1 digit cetakan]-[2 digit tahun]-[2 digit bulan]-[6 digit nomor terakhir]
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
