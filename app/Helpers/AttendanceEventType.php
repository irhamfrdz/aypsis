<?php

namespace App\Helpers;

class AttendanceEventType
{
    public static function fromMachineCode($code): ?string
    {
        $code = trim((string) $code);
        if ($code === 'b') {
            return 'istirahat_masuk';
        }

        return match (strtoupper($code)) {
            '0', 'I', 'IN', 'C/IN', 'MASUK' => 'Masuk',
            '1', 'O', 'OUT', 'C/OUT', 'PULANG' => 'Pulang',
            '2', 'B' => 'istirahat_keluar',
            '3' => 'istirahat_masuk',
            '4' => 'lembur_masuk',
            '5' => 'lembur_pulang',
            default => self::normalize($code),
        };
    }

    public static function normalize(string $type): ?string
    {
        return match (strtolower(str_replace('_', ' ', trim($type)))) {
            'masuk', 'check in' => 'Masuk',
            'pulang', 'keluar', 'check out' => 'Pulang',
            'istirahat keluar' => 'istirahat_keluar',
            'istirahat masuk' => 'istirahat_masuk',
            'lembur masuk', 'mulai lembur', 'lembur' => 'lembur_masuk',
            'lembur pulang', 'selesai lembur', 'lembur keluar' => 'lembur_pulang',
            default => null,
        };
    }
}
