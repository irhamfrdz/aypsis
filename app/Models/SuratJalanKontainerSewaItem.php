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
    ];

    // ─── Relationships ────────────────────────────────────────────────

    /**
     * Surat Jalan parent
     */
    public function suratJalan()
    {
        return $this->belongsTo(SuratJalanKontainerSewa::class, 'surat_jalan_kontainer_sewa_id');
    }
}
