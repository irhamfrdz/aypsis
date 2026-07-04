<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KapalSpkbm extends Model
{
    protected $table = 'kapal_spkbms';

    protected $fillable = [
        'kapal_id',
        'nomor_surat',
        'hal',
        'ditujukan_kepada',
        'voyage',
        'rencana_tiba',
        'rencana_sandar',
        'rencana_bongkar',
        'rencana_muat',
        'tujuan',
    ];

    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'kapal_id');
    }

    /**
     * Generate next nomor surat spkbm.
     * Format: {seq}/AYP-SPKBM/{roman_month}/{year}
     */
    public static function generateNomor(): string
    {
        $year = now()->format('Y');
        $month = (int) now()->format('n');

        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $romanMonth = $romanMonths[$month] ?? 'I';

        $lastRecord = self::whereYear('created_at', $year)
            ->orderByDesc('id')
            ->first();

        $nextSeq = 1;
        if ($lastRecord) {
            $parts = explode('/', $lastRecord->nomor_surat);
            if (count($parts) > 0 && is_numeric($parts[0])) {
                $nextSeq = ((int) $parts[0]) + 1;
            }
        }

        $paddedSeq = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);

        return "{$paddedSeq}/AYP-SPKBM/{$romanMonth}/{$year}";
    }
}
