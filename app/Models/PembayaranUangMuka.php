<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class PembayaranUangMuka extends Model
{
    use HasFactory, Auditable;

    protected $table = 'pembayaran_uang_muka';

    protected $fillable = [
        'nomor_pembayaran',
        'tanggal_pembayaran',
        'kas_bank_id',
        'jenis_transaksi',
        'kegiatan',
        'mobil_id',
        'penerima_id',
        'supir_ids',
        'jumlah_per_supir',
        'total_pembayaran',
        'keterangan',
        'status',
        'dibuat_oleh',
        'disetujui_oleh',
        'tanggal_persetujuan',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'tanggal_persetujuan' => 'datetime',
        'supir_ids' => 'array', // JSON array untuk multi-select supir
        'jumlah_per_supir' => 'array', // JSON object untuk jumlah per supir (supir_id => jumlah)
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

    public function masterKegiatan()
    {
        return $this->belongsTo(MasterKegiatan::class, 'kegiatan');
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }

    public function penerima()
    {
        return $this->belongsTo(Karyawan::class, 'penerima_id');
    }

    // Get supir list from supir_ids
    public function supirList()
    {
        if (empty($this->supir_ids)) {
            return collect();
        }

        return \App\Models\Karyawan::whereIn('id', $this->supir_ids)->get();
    }

    // Check if status is already used
    public function isUsed()
    {
        return $this->status === 'uang_muka_terpakai';
    }

    // Mark as used
    public function markAsTerpakai()
    {
        $this->update(['status' => 'uang_muka_terpakai']);
    }

    // Mark as unused
    public function markAsBelumTerpakai()
    {
        $this->update(['status' => 'uang_muka_belum_terpakai']);
    }

    // Generate unique nomor pembayaran
    public static function generateNomorPembayaran($kasBankId)
    {
        $today = now();
        $tahun = $today->format('y'); // 2 digit year
        $bulan = $today->format('m'); // 2 digit month

        // Get COA info untuk kode bank
        $coa = \App\Models\Coa::find($kasBankId);
        $kodeBank = $coa ? $coa->kode_nomor : '000';

        // Get next running number from existing nomor_pembayaran modul
        $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'nomor_pembayaran')->first();

        if (!$nomorTerakhir) {
            // Create if not exists
            $nomorTerakhir = \App\Models\NomorTerakhir::create([
                'modul' => 'nomor_pembayaran',
                'nomor_terakhir' => 1
            ]);
            $nextNumber = 1;
        } else {
            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $nomorTerakhir->update(['nomor_terakhir' => $nextNumber]);
        }

        $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return "UM-{$kodeBank}-{$bulan}-{$tahun}-{$sequence}";
    }

    // Scope untuk yang belum terpakai
    public function scopeBelumTerpakai($query)
    {
        return $query->where('status', 'uang_muka_belum_terpakai');
    }

    // Scope untuk yang sudah terpakai
    public function scopeTerpakai($query)
    {
        return $query->where('status', 'uang_muka_terpakai');
    }

    // Format kegiatan untuk display
    public function getFormattedKegiatanAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->kegiatan));
    }

    // Format nomor untuk display
    public function getFormattedNomorAttribute()
    {
        return $this->nomor_pembayaran;
    }

    // Get total supir count
    public function getTotalSupirAttribute()
    {
        return is_array($this->supir_ids) ? count($this->supir_ids) : 0;
    }

    // Status color for UI
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'uang_muka_belum_terpakai' => 'green',
            'uang_muka_terpakai' => 'gray',
            default => 'blue'
        };
    }

    // Status text for UI
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'uang_muka_belum_terpakai' => 'Belum Terpakai',
            'uang_muka_terpakai' => 'Sudah Terpakai',
            default => 'Unknown'
        };
    }
}
