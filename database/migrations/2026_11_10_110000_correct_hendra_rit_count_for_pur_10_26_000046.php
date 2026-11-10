<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const NO_PRANOTA = 'PUR-10-26-000046';

    public function up(): void
    {
        DB::transaction(function (): void {
            $details = DB::table('pranota_uang_rit_supir_details as details')
                ->leftJoin('karyawans', function ($join): void {
                    $join->whereRaw('LOWER(TRIM(karyawans.nama_lengkap)) = LOWER(TRIM(details.supir_nama))')
                        ->orWhereRaw('LOWER(TRIM(karyawans.nama_panggilan)) = LOWER(TRIM(details.supir_nama))');
                })
                ->where('details.no_pranota', self::NO_PRANOTA)
                ->where(function ($query): void {
                    $query->whereRaw('LOWER(TRIM(details.supir_nama)) LIKE ?', ['%hendra%'])
                        ->orWhereRaw('LOWER(TRIM(karyawans.nama_lengkap)) LIKE ?', ['%hendra%'])
                        ->orWhereRaw('LOWER(TRIM(karyawans.nama_panggilan)) LIKE ?', ['%hendra%']);
                })
                ->select('details.id', 'details.jumlah_rit')
                ->distinct()
                ->lockForUpdate()
                ->get();

            if ($details->count() !== 1) {
                throw new RuntimeException('Detail Hendra untuk '.self::NO_PRANOTA.' harus ditemukan tepat satu; ditemukan '.$details->count().'.');
            }

            $detail = $details->first();
            if ((int) $detail->jumlah_rit === 8) {
                return;
            }
            if ((int) $detail->jumlah_rit !== 7) {
                throw new RuntimeException('Jumlah rit Hendra harus 7 sebelum koreksi; ditemukan '.$detail->jumlah_rit.'.');
            }

            DB::table('pranota_uang_rit_supir_details')
                ->where('id', $detail->id)
                ->update(['jumlah_rit' => 8, 'updated_at' => now()]);
        });
    }

    public function down(): void
    {
        DB::table('pranota_uang_rit_supir_details as details')
            ->where('details.no_pranota', self::NO_PRANOTA)
            ->where('details.jumlah_rit', 8)
            ->where(function ($query): void {
                $query->whereRaw('LOWER(TRIM(details.supir_nama)) LIKE ?', ['%hendra%'])
                    ->orWhereExists(function ($subquery): void {
                        $subquery->selectRaw('1')
                            ->from('karyawans')
                            ->whereRaw('LOWER(TRIM(karyawans.nama_lengkap)) LIKE ?', ['%hendra%'])
                            ->whereRaw('LOWER(TRIM(karyawans.nama_lengkap)) = LOWER(TRIM(details.supir_nama))');
                    })
                    ->orWhereExists(function ($subquery): void {
                        $subquery->selectRaw('1')
                            ->from('karyawans')
                            ->whereRaw('LOWER(TRIM(karyawans.nama_panggilan)) LIKE ?', ['%hendra%'])
                            ->whereRaw('LOWER(TRIM(karyawans.nama_panggilan)) = LOWER(TRIM(details.supir_nama))');
                    });
            })
            ->update(['jumlah_rit' => 7, 'updated_at' => now()]);
    }
};
