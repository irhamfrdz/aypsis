<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AbsensiDeletionGuard
{
    public static function normalizeType(string $type): string
    {
        $type = strtolower(trim(str_replace('_', ' ', $type)));

        return match ($type) {
            'check in', 'in' => 'masuk',
            'check out', 'out', 'keluar' => 'pulang',
            'mulai lembur', 'lembur' => 'lembur masuk',
            'selesai lembur', 'lembur keluar' => 'lembur pulang',
            default => $type,
        };
    }

    public static function key(string $nik, string $time, string $type): string
    {
        return $nik.'_'.Carbon::parse($time)->format('Y-m-d H:i:s').'_'.self::normalizeType($type);
    }

    public static function deletedKeys(): array
    {
        $keys = [];
        foreach (DB::table('absensi_deletion_marks')->select('nik', 'waktu', 'tipe')->cursor() as $mark) {
            $keys[self::key($mark->nik, $mark->waktu, $mark->tipe)] = true;
        }

        return $keys;
    }

    public static function deletedTimestampKeys(): array
    {
        $keys = [];
        foreach (DB::table('absensi_deletion_marks')->select('nik', 'waktu')->cursor() as $mark) {
            $keys[$mark->nik.'_'.Carbon::parse($mark->waktu)->format('Y-m-d H:i:s')] = true;
        }

        return $keys;
    }

    public static function isDeleted(string $nik, string $time, string $type): bool
    {
        return DB::table('absensi_deletion_marks')
            ->where('nik', $nik)
            ->where('waktu', Carbon::parse($time)->format('Y-m-d H:i:s'))
            ->where('tipe', self::normalizeType($type))
            ->exists();
    }
}
