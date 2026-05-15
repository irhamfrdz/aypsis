<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuratJalanKontainerSewaItem extends Model
{
    use HasFactory;

    protected $table = 'surat_jalan_kontainer_sewa_items';

    protected $fillable = [
        'surat_jalan_kontainer_sewa_id',
        'nomor_kontainer',
        'ukuran',
        'tipe_kontainer',
        'vendor',
        'kondisi',
        'catatan_kondisi',
    ];

    // ─── Relationships ────────────────────────────────────────────────

    /**
     * Surat Jalan parent
     */
    public function suratJalan()
    {
        return $this->belongsTo(SuratJalanKontainerSewa::class, 'surat_jalan_kontainer_sewa_id');
    }

    // ─── Accessors ────────────────────────────────────────────────────

    public function getKondisiLabelAttribute()
    {
        return match($this->kondisi) {
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            default => ucfirst($this->kondisi),
        };
    }

    public function getKondisiBadgeAttribute()
    {
        return match($this->kondisi) {
            'baik' => 'bg-green-100 text-green-700',
            'rusak_ringan' => 'bg-yellow-100 text-yellow-700',
            'rusak_berat' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
