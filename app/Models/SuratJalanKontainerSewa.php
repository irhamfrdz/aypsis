<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratJalanKontainerSewa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_jalan_kontainer_sewas';

    protected $fillable = [
        'nomor_surat_jalan',
        'tipe',
        'tanggal',
        'vendor',
        'supir',
        'no_plat',
        'antar_lokasi',
        'tujuan',
        'menggunakan_rit',
        'nominal_uang_jalan',
        'nomor_kontainer',
        'ukuran',
        'tipe_kontainer',
        'vendor_item',
        'kondisi',
        'catatan_kondisi',
        'lokasi_pengambilan',
        'lokasi_pengembalian',
        'keterangan',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ─── Relationships ────────────────────────────────────────────────

    /**
     * Detail items (kontainer) yang termasuk dalam SJ ini
     */
    public function items()
    {
        return $this->hasMany(SuratJalanKontainerSewaItem::class);
    }

    /**
     * User yang membuat
     */
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User yang update terakhir
     */
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ─── Accessors ────────────────────────────────────────────────────

    public function getTipeLabelAttribute()
    {
        return $this->tipe === 'pengambilan' ? 'Pengambilan' : 'Pengembalian';
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'aktif' => 'Aktif',
            'selesai' => 'Selesai',
            'batal' => 'Batal',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-700',
            'aktif' => 'bg-blue-100 text-blue-700',
            'selesai' => 'bg-green-100 text-green-700',
            'batal' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getTipeBadgeAttribute()
    {
        return $this->tipe === 'pengambilan'
            ? 'bg-emerald-100 text-emerald-700'
            : 'bg-orange-100 text-orange-700';
    }

    // ─── Helpers ──────────────────────────────────────────────────────

    /**
     * Generate nomor surat jalan otomatis
     * Format: SJ-KS-{PENGAMBILAN/PENGEMBALIAN}/{YYYYMM}/{SEQUENCE}
     */
    public static function generateNomor(string $tipe): string
    {
        $prefix = $tipe === 'pengambilan' ? 'SJ-KS-AMB' : 'SJ-KS-KMB';
        $bulan = now()->format('Ym');

        $lastRecord = self::where('nomor_surat_jalan', 'like', "{$prefix}/{$bulan}/%")
            ->orderByDesc('id')
            ->first();

        if ($lastRecord) {
            $lastSeq = (int) last(explode('/', $lastRecord->nomor_surat_jalan));
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return "{$prefix}/{$bulan}/" . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }
}
