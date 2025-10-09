<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PembayaranOb extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_obs';

    protected $fillable = [
        'nomor_pembayaran',
        'tanggal_pembayaran',
        'kas_bank_id',
        'jenis_transaksi',
        'supir_ids',
        'jumlah_per_supir',
        'subtotal_pembayaran',
        'dp_amount',
        'total_pembayaran',
        'keterangan',
        'pembayaran_dp_ob_id',
        'status',
        'dibuat_oleh',
        'disetujui_oleh',
        'tanggal_persetujuan',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'tanggal_persetujuan' => 'datetime',
        'supir_ids' => 'array', // JSON array untuk multi-select supir
        'jumlah_per_supir' => 'decimal:2',
        'subtotal_pembayaran' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'total_pembayaran' => 'decimal:2',
    ];

    // Relationships
    public function kasBankAkun()
    {
        return $this->belongsTo(Coa::class, 'kas_bank_id');
    }

    public function pembuatPembayaran()
    {
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh');
    }

    public function penyetujuPembayaran()
    {
        return $this->belongsTo(\App\Models\User::class, 'disetujui_oleh');
    }

    public function pembayaranDpOb()
    {
        return $this->belongsTo(PembayaranDpOb::class, 'pembayaran_dp_ob_id');
    }

    public function supirList()
    {
        return \App\Models\Karyawan::whereIn('id', $this->supir_ids ?? [])->get();
    }

    // Helper methods
    public function calculateTotalPembayaran()
    {
        $jumlahSupir = count($this->supir_ids ?? []);
        return $jumlahSupir * $this->jumlah_per_supir;
    }

    /**
     * Generate nomor pembayaran menggunakan master nomor terakhir dengan modul nomor_pembayaran
     */
    public static function generateNomorPembayaran($coaId)
    {
        $today = now();
        $tahun = $today->format('y'); // 2 digit year
        $bulan = $today->format('m'); // 2 digit month

        // Get COA info untuk kode bank
        $coa = \App\Models\Coa::find($coaId);
        if (!$coa) {
            throw new \Exception('COA tidak ditemukan.');
        }

        // Ambil kode_nomor dari COA sebagai kode bank
        $kodeBank = $coa->kode_nomor ?? '000';

        // Get next nomor pembayaran from master nomor terakhir dengan modul nomor_pembayaran
        $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'nomor_pembayaran')
            ->lockForUpdate()
            ->first();

        if (!$nomorTerakhir) {
            throw new \Exception('Modul nomor_pembayaran tidak ditemukan di master nomor terakhir.');
        }

        $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
        $nomorTerakhir->nomor_terakhir = $nextNumber;
        $nomorTerakhir->save();

        $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return "{$kodeBank}-{$bulan}-{$tahun}-{$sequence}";
    }
}
