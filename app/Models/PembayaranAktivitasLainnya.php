<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class PembayaranAktivitasLainnya extends Model
{
    use HasFactory;

    use Auditable;
    protected $table = 'pembayaran_aktivitas_lainnya';

    protected $fillable = [
        'nomor_pembayaran',
        'tanggal_pembayaran',
        'total_pembayaran',
        'pilih_bank',
        'jenis_transaksi',
        'aktivitas_pembayaran',
        'kegiatan',
        'plat_nomor',
        'nomor_accurate',
        'is_dp',
        'created_by'
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'total_pembayaran' => 'decimal:2',
        'is_dp' => 'boolean'
    ];

    /**
     * Relationship dengan aktivitas lainnya (many-to-many)
     */
    public function aktivitasLainnya()
    {
        return $this->belongsToMany(AktivitasLainnya::class, 'pembayaran_aktivitas_lainnya_items', 'pembayaran_id', 'aktivitas_id')
                    ->withPivot('nominal', 'keterangan')
                    ->withTimestamps();
    }

    /**
     * Relationship dengan User (created_by)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Alias for creator relationship to maintain consistency with other payment models
     */
    public function pembuatPembayaran()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship dengan COA Bank (pilih_bank)
     */
    public function bank()
    {
        return $this->belongsTo(Coa::class, 'pilih_bank');
    }

    /**
     * Relationship dengan items pembayaran
     */
    public function items()
    {
        return $this->hasMany(PembayaranAktivitasLainnyaItem::class, 'pembayaran_id');
    }

    /**
     * Get the bank account name for display
     */
    public function getBankAccountAttribute()
    {
        return $this->bank ? $this->bank->nomor_akun . ' - ' . $this->bank->nama_akun : null;
    }

    /**
     * Generate nomor pembayaran otomatis
     */
    public static function generateNomorPembayaran()
    {
        $date = now();
        $prefix = 'PAL/' . $date->format('Y/m') . '/';

        $lastRecord = self::where('nomor_pembayaran', 'like', $prefix . '%')
            ->orderBy('nomor_pembayaran', 'desc')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->nomor_pembayaran, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Generate nomor pembayaran dengan format COA code-MM-YY-NNNNNN
     * Format: [kode bank dari COA]-[2 digit bulan]-[2 digit tahun]-[6 digit running number]
     * Example: TST-10-25-000001
     */
    public static function generateNomorPembayaranCoa($coaId)
    {
        $today = now();
        $tahun = $today->format('y'); // 2 digit year
        $bulan = $today->format('m'); // 2 digit month

        // Get COA info untuk kode bank
        $coa = \App\Models\Coa::find($coaId);
        if (!$coa) {
            throw new \Exception('COA tidak ditemukan.');
        }

        // Ambil kode_nomor dari COA sebagai kode bank (sama seperti pembayaran kontainer)
        $kodeBank = $coa->kode_nomor ?? '000';

        // Get next nomor pembayaran from master nomor terakhir
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

    /**
     * Calculate total from items
     */
    public function calculateTotal()
    {
        return $this->items->sum('nominal');
    }

    /**
     * Scope untuk filter status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter tanggal
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_pembayaran', [$startDate, $endDate]);
    }
}
