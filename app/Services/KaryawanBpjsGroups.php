<?php

namespace App\Services;

use App\Models\Karyawan;

class KaryawanBpjsGroups
{
    private const DEFAULTS = [
        'group_jkn' => [
            'JKN-KIS-HARIAN', 'JKN-KIS-KANTOR', 'JKN-KIS-LAPANGAN',
            'JKN-KIS-NON KARY', 'JKN-KIS-NON KARY-UMKM KIS',
            'JKN-KIS-TRANSFER', 'JKN-KIS-TUNAI', 'JKN-REIMBURSEMENT-CREW',
        ],
        'group_bp_jamsostek' => [
            'BPU-CREW', 'BPU-HARIAN', 'BPU-LAPANGAN', 'BPU-NON KARY-PBM',
            'BPU-NON KARY-UMKM', 'BPU-NON KARY-UMKM NO PP', 'BPU-TUNAI',
            'PPU-HARIAN', 'PPU-KANTOR', 'PPU-LAPANGAN', 'PPU-TRANSFER', 'PPU-TUNAI',
        ],
    ];

    public static function normalize(string $value): string
    {
        return mb_strtoupper(preg_replace('/\s+/u', ' ', trim($value)));
    }

    public function options(): array
    {
        $options = [];
        foreach (self::DEFAULTS as $column => $defaults) {
            $options[$column] = collect($defaults)
                ->merge(Karyawan::whereNotNull($column)->distinct()->pluck($column))
                ->map(fn ($value) => self::normalize($value))
                ->filter()->unique()->values()->all();
        }

        return $options;
    }
}
