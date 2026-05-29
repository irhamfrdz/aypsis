<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TandaTerimaSuratJalanKontainerSewa extends Model
{
    use HasFactory;

    protected $table = 'tanda_terima_surat_jalan_kontainer_sewas';

    protected $fillable = [
        'nomor_tanda_terima',
        'tanggal_tanda_terima',
        'tanggal_mulai_sewa',
        'surat_jalan_kontainer_sewa_id',
        'nomor_surat_jalan',
        'nomor_kontainer',
        'tipe_kontainer',
        'ukuran',
        'supir',
        'no_plat',
        'kegiatan',
        'status',
        'keterangan',
        'lembur',
        'nginap',
        'tidak_lembur_nginap',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_tanda_terima' => 'date',
        'tanggal_mulai_sewa' => 'date',
        'lembur' => 'boolean',
        'nginap' => 'boolean',
        'tidak_lembur_nginap' => 'boolean',
    ];

    /**
     * Relationship to SuratJalanKontainerSewa
     */
    public function suratJalanKontainerSewa(): BelongsTo
    {
        return $this->belongsTo(SuratJalanKontainerSewa::class, 'surat_jalan_kontainer_sewa_id');
    }

    /**
     * User yang membuat
     */
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User yang update terakhir
     */
    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
