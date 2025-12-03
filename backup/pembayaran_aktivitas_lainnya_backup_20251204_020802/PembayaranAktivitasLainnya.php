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
        'is_dp',
        'status',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'total_pembayaran' => 'decimal:2',
        'is_dp' => 'boolean',
        'approved_at' => 'datetime'
    ];

    /**
     * Relationship dengan supir (untuk daftar uang muka supir)
     */
    public function supirList()
    {
        return $this->hasMany(PembayaranAktivitasLainnyaSupir::class, 'pembayaran_id');
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
     * Relationship dengan User (approved_by)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship dengan COA Bank (pilih_bank)
     */
    public function bank()
    {
        return $this->belongsTo(Coa::class, 'pilih_bank');
    }

    /**
     * Relationship dengan COA Biaya
     */
    public function akunBiaya()
    {
        return $this->belongsTo(Coa::class, 'akun_biaya_id');
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
    /**
     * @deprecated Use generateNomorPembayaranCoa() instead
     * Generate nomor pembayaran dengan format baru PMS[kode]MMYYNNNNNN
     * This method now calls generateNomorPembayaranCoa with default bank ID
     */
    public static function generateNomorPembayaran()
    {
        // Get default bank account (first available)
        $defaultBank = \App\Models\Coa::where('tipe_akun', 'LIKE', '%Kas%')
            ->orWhere('tipe_akun', 'LIKE', '%Bank%')
            ->first();
        
        if (!$defaultBank) {
            throw new \Exception('Tidak ada akun bank/kas tersedia. Silakan gunakan generateNomorPembayaranCoa() dengan COA ID yang valid.');
        }
        
        return self::generateNomorPembayaranCoa($defaultBank->id);
    }

    /**
     * Generate nomor pembayaran dengan format PMS[kode]-MMYY-NNNNNN
     * Format: 3 digit kode nomor (dari master nomor terakhir modul PMS), 2 digit bulan, 2 digit tahun, 6 digit running number
     * Example: PMS1116000001
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

        // Ambil kode_nomor dari COA sebagai kode bank (3 digit)
        $kodeBank = $coa->kode_nomor ?? 'PMS';

        // Get next nomor pembayaran from master nomor terakhir untuk modul PMS
        $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'pembayaran_aktivitas_lainnya_pms')
            ->lockForUpdate()
            ->first();

        if (!$nomorTerakhir) {
            throw new \Exception('Modul pembayaran_aktivitas_lainnya_pms tidak ditemukan di master nomor terakhir.');
        }

        $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
        $nomorTerakhir->nomor_terakhir = $nextNumber;
        $nomorTerakhir->save();

        $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        // Format: PMS1116000001 (kode+bulan+tahun+running number)
        return "{$kodeBank}{$bulan}{$tahun}{$sequence}";
    }

    /**
     * Calculate total from supir list
     */
    public function calculateTotalUangMukaSupir()
    {
        return $this->supirList->sum('jumlah_uang_muka');
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

    /**
     * Scope untuk filter voyage
     */
    public function scopeByVoyage($query, $voyage)
    {
        return $query->where('nomor_voyage', $voyage);
    }
}
