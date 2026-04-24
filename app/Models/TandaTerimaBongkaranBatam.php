<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class TandaTerimaBongkaranBatam extends Model
{
    use HasFactory, Auditable;

    protected $table = 'tanda_terima_bongkaran_batams';

    protected $fillable = [
        'nomor_tanda_terima',
        'tanggal_tanda_terima',
        'surat_jalan_bongkaran_id',
        'gudang_id',
        'no_kontainer',
        'no_seal',
        'kegiatan',
        'status',
        'keterangan',
        'lembur',
        'nginap',
        'tidak_lembur_nginap',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal_tanda_terima' => 'date',
        'lembur' => 'boolean',
        'nginap' => 'boolean',
        'tidak_lembur_nginap' => 'boolean',
    ];

    /**
     * Relationship to SuratJalanBongkaran
     */
    public function suratJalanBongkaran(): BelongsTo
    {
        return $this->belongsTo(SuratJalanBongkaranBatam::class, 'surat_jalan_bongkaran_id');
    }

    /**
     * Relationship to Gudang
     */
    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class, 'gudang_id');
    }

    /**
     * Relationship to User (Creator)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to User (Updater)
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Generate automatic tanda terima number
     */
    public static function generateNoTandaTerima()
    {
        $year = date('Y');
        $month = date('m');

        $lastRecord = self::whereYear('tanggal_tanda_terima', $year)
                         ->whereMonth('tanggal_tanda_terima', $month)
                         ->orderBy('nomor_tanda_terima', 'desc')
                         ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->nomor_tanda_terima, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'TTB-B/' . $year . '/' . $month . '/' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
