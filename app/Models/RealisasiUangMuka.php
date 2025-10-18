<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


use App\Traits\Auditable;
class RealisasiUangMuka extends Model
{
    use HasFactory;

    use Auditable;
    protected $table = 'realisasi_uang_muka';

    protected $fillable = [
        'kegiatan',
        'nomor_pembayaran',
        'tanggal_pembayaran',
        'kas_bank_id',
        'jenis_transaksi',
        'supir_ids',
        'jumlah_per_supir',
        'keterangan_per_supir',
        'total_pembayaran',
        'keterangan',
        'item_type',
        'status',
        'dibuat_oleh',
        'disetujui_oleh',
        'tanggal_persetujuan',
        'dp_amount',
        'pembayaran_uang_muka_id',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'tanggal_persetujuan' => 'datetime',
        'supir_ids' => 'array', // JSON array untuk multi-select supir
        'jumlah_per_supir' => 'array', // JSON object untuk jumlah per supir (supir_id => jumlah)
        'keterangan_per_supir' => 'array', // JSON object untuk keterangan per supir (supir_id => keterangan)
        'total_pembayaran' => 'decimal:2',
        'dp_amount' => 'decimal:2',
    ];

    // Relationships
    public function masterKegiatan()
    {
        return $this->belongsTo(MasterKegiatan::class, 'kegiatan');
    }

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

    public function pembayaranUangMuka()
    {
        return $this->belongsTo(PembayaranUangMuka::class, 'pembayaran_uang_muka_id');
    }

    public function realisasiDetails()
    {
        return $this->hasMany(RealisasiUangMukaDetail::class, 'realisasi_uang_muka_id');
    }

    public function supirList()
    {
        return \App\Models\Karyawan::whereIn('id', $this->supir_ids ?? [])->get();
    }

    // Helper methods
    public function calculateTotalRealisasi()
    {
        if (is_array($this->jumlah_per_supir)) {
            return array_sum($this->jumlah_per_supir);
        }
        return 0;
    }

    /**
     * Generate nomor pembayaran menggunakan master nomor terakhir dengan modul realisasi_uang_muka
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

        // Get next nomor pembayaran from master nomor terakhir dengan modul nomor_pembayaran (sama dengan pembayaran uang muka)
        $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'nomor_pembayaran')
            ->lockForUpdate()
            ->first();

        if (!$nomorTerakhir) {
            // Create new entry if not exists
            $nomorTerakhir = \App\Models\NomorTerakhir::create([
                'modul' => 'nomor_pembayaran',
                'nomor_terakhir' => 0,
                'keterangan' => 'Nomor terakhir untuk pembayaran'
            ]);
        }

        $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
        $nomorTerakhir->nomor_terakhir = $nextNumber;
        $nomorTerakhir->save();

        $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return "{$kodeBank}-{$bulan}-{$tahun}-{$sequence}";
    }

    // Status methods
    public function markAsSelesai()
    {
        $this->update(['status' => 'selesai']);
    }

    public function markAsBelumSelesai()
    {
        $this->update(['status' => 'belum_selesai']);
    }

    public function isSelesai()
    {
        return $this->status === 'selesai';
    }

    public function isBelumSelesai()
    {
        return $this->status === 'belum_selesai';
    }

    // Scope untuk filter berdasarkan status
    public function scopeBelumSelesai($query)
    {
        return $query->where('status', 'belum_selesai');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }
}
