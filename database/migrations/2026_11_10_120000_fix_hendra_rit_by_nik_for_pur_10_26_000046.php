<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const NO_PRANOTA = 'PUR-10-26-000046';
    private const NIK = '1351';

    public function up(): void
    {
        DB::transaction(function (): void {
            $details = DB::table('pranota_uang_rit_supir_details as details')
                ->join('karyawans', function ($join): void {
                    $join->whereRaw('LOWER(TRIM(karyawans.nama_lengkap)) = LOWER(TRIM(details.supir_nama))')
                        ->orWhereRaw('LOWER(TRIM(karyawans.nama_panggilan)) = LOWER(TRIM(details.supir_nama))');
                })
                ->where('details.no_pranota', self::NO_PRANOTA)
                ->where('karyawans.nik', self::NIK)
                ->select('details.id', 'details.jumlah_rit')
                ->distinct()
                ->lockForUpdate()
                ->get();

            if ($details->count() !== 1) {
                throw new RuntimeException('Detail supir NIK '.self::NIK.' pada '.self::NO_PRANOTA.' harus ditemukan tepat satu; ditemukan '.$details->count().'.');
            }

            $detail = $details->first();
            if ((int) $detail->jumlah_rit === 8) {
                return;
            }
            if ((int) $detail->jumlah_rit !== 7) {
                throw new RuntimeException('Jumlah rit awal supir NIK '.self::NIK.' harus 7; ditemukan '.$detail->jumlah_rit.'.');
            }

            DB::table('pranota_uang_rit_supir_details')
                ->where('id', $detail->id)
                ->update(['jumlah_rit' => 8, 'updated_at' => now()]);
        });
    }

    public function down(): void
    {
        DB::table('pranota_uang_rit_supir_details as details')
            ->join('karyawans', function ($join): void {
                $join->whereRaw('LOWER(TRIM(karyawans.nama_lengkap)) = LOWER(TRIM(details.supir_nama))')
                    ->orWhereRaw('LOWER(TRIM(karyawans.nama_panggilan)) = LOWER(TRIM(details.supir_nama))');
            })
            ->where('details.no_pranota', self::NO_PRANOTA)
            ->where('details.jumlah_rit', 8)
            ->where('karyawans.nik', self::NIK)
            ->update(['details.jumlah_rit' => 7, 'details.updated_at' => now()]);
    }
};
