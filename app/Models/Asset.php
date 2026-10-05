<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $table = 'assets';

    protected $fillable = [
        'kode_asset',
        'nama_asset',
        'kategori',
        'merk',
        'tipe_model',
        'nomor_seri',
        'lokasi',
        'tanggal_perolehan',
        'nilai_perolehan',
        'masa_manfaat_bulan',
        'nilai_residu',
        'nilai_buku',
        'kondisi',
        'status',
        'penanggung_jawab',
        'karyawan_id',
        'vendor',
        'nomor_faktur',
        'foto',
        'lampiran',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
        'nilai_perolehan' => 'decimal:2',
        'nilai_residu' => 'decimal:2',
        'nilai_buku' => 'decimal:2',
        'masa_manfaat_bulan' => 'integer',
    ];

    public const KATEGORI_OPTIONS = [
        'Elektronik & IT',
        'Kendaraan',
        'Alat Berat',
        'Mesin & Peralatan',
        'Furniture & Fixture',
        'Bangunan & Properti',
        'Lain-lain',
    ];

    public const KONDISI_OPTIONS = [
        'Baik',
        'Rusak Ringan',
        'Rusak Berat',
        'Afkir',
    ];

    public const STATUS_OPTIONS = [
        'Tersedia',
        'Digunakan',
        'Dalam Pemeliharaan',
        'Dipinjam',
        'Dijual',
        'Dihapuskan',
    ];

    /**
     * Relationship to PIC Karyawan
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    /**
     * Relationship to User creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to User updater
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Generate next asset code
     */
    public static function generateNextKode(): string
    {
        $lastAsset = self::withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        if (! $lastAsset) {
            return 'AST-0001';
        }

        // Try extracting numeric portion
        if (preg_match('/AST-(\d+)/', $lastAsset->kode_asset, $matches)) {
            $nextNumber = intval($matches[1]) + 1;

            return 'AST-'.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
        }

        return 'AST-'.str_pad((string) ($lastAsset->id + 1), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Badge CSS class for Status
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Tersedia' => 'bg-green-100 text-green-800 border-green-200',
            'Digunakan' => 'bg-blue-100 text-blue-800 border-blue-200',
            'Dalam Pemeliharaan' => 'bg-amber-100 text-amber-800 border-amber-200',
            'Dipinjam' => 'bg-purple-100 text-purple-800 border-purple-200',
            'Dijual' => 'bg-gray-100 text-gray-800 border-gray-200',
            'Dihapuskan' => 'bg-red-100 text-red-800 border-red-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    /**
     * Badge CSS class for Kondisi
     */
    public function getKondisiBadgeClassAttribute(): string
    {
        return match ($this->kondisi) {
            'Baik' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'Rusak Ringan' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'Rusak Berat' => 'bg-red-100 text-red-800 border-red-200',
            'Afkir' => 'bg-zinc-100 text-zinc-800 border-zinc-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    /**
     * PIC name fallback
     */
    public function getPicNameAttribute(): string
    {
        if ($this->karyawan) {
            return $this->karyawan->nama_lengkap ?? $this->karyawan->nama ?? $this->penanggung_jawab ?? '-';
        }

        return $this->penanggung_jawab ?? '-';
    }
}
