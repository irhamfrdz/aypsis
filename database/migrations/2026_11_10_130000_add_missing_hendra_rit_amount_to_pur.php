<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const NO_PRANOTA = 'PUR-10-26-000046';
    private const NIK = '1351';
    private const RIT_AMOUNT = 85000;

    public function up(): void
    {
        DB::transaction(function (): void {
            $detail = DB::table('pranota_uang_rit_supir_details as details')
                ->join('karyawans', function ($join): void {
                    $join->whereRaw('LOWER(TRIM(karyawans.nama_lengkap)) = LOWER(TRIM(details.supir_nama))')
                        ->orWhereRaw('LOWER(TRIM(karyawans.nama_panggilan)) = LOWER(TRIM(details.supir_nama))');
                })
                ->where('details.no_pranota', self::NO_PRANOTA)
                ->where('karyawans.nik', self::NIK)
                ->select('details.id', 'details.jumlah_rit', 'details.total_uang_supir')
                ->lockForUpdate()
                ->first();

            if (! $detail) {
                throw new RuntimeException('Detail Hendra (NIK '.self::NIK.') tidak ditemukan pada '.self::NO_PRANOTA.'.');
            }
            if ((int) $detail->jumlah_rit === 8 && (float) $detail->total_uang_supir >= 680000) {
                return;
            }
            if ((int) $detail->jumlah_rit !== 8 || (float) $detail->total_uang_supir !== 595000.0) {
                throw new RuntimeException('Kondisi detail Hendra tidak sesuai. Jumlah rit/total uang saat ini: '.$detail->jumlah_rit.' / '.$detail->total_uang_supir.'. Tidak ada perubahan dilakukan.');
            }

            DB::table('pranota_uang_rit_supir_details')->where('id', $detail->id)->update([
                'total_uang_supir' => self::RIT_AMOUNT + (float) $detail->total_uang_supir,
                'grand_total' => self::RIT_AMOUNT + (float) $detail->total_uang_supir - (float) DB::table('pranota_uang_rit_supir_details')->where('id', $detail->id)->value('hutang')
                    - (float) DB::table('pranota_uang_rit_supir_details')->where('id', $detail->id)->value('tabungan')
                    - (float) DB::table('pranota_uang_rit_supir_details')->where('id', $detail->id)->value('bpjs')
                    + (float) DB::table('pranota_uang_rit_supir_details')->where('id', $detail->id)->value('adjustment'),
                'updated_at' => now(),
            ]);

            DB::table('pranota_uang_rits')->where('no_pranota', self::NO_PRANOTA)->update([
                'uang_rit_supir' => DB::raw('uang_rit_supir + '.self::RIT_AMOUNT),
                'total_rit' => DB::raw('total_rit + '.self::RIT_AMOUNT),
                'total_uang' => DB::raw('total_uang + '.self::RIT_AMOUNT),
                'grand_total_bersih' => DB::raw('grand_total_bersih + '.self::RIT_AMOUNT),
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            $detail = DB::table('pranota_uang_rit_supir_details as details')
                ->join('karyawans', function ($join): void {
                    $join->whereRaw('LOWER(TRIM(karyawans.nama_lengkap)) = LOWER(TRIM(details.supir_nama))')
                        ->orWhereRaw('LOWER(TRIM(karyawans.nama_panggilan)) = LOWER(TRIM(details.supir_nama))');
                })
                ->where('details.no_pranota', self::NO_PRANOTA)
                ->where('karyawans.nik', self::NIK)
                ->select('details.id', 'details.total_uang_supir', 'details.hutang', 'details.tabungan', 'details.bpjs', 'details.adjustment')
                ->first();

            if (! $detail || (float) $detail->total_uang_supir < 680000) {
                return;
            }

            $updatedTotal = (float) $detail->total_uang_supir - self::RIT_AMOUNT;
            $grandTotal = $updatedTotal - (float) $detail->hutang - (float) $detail->tabungan - (float) $detail->bpjs + (float) $detail->adjustment;
            DB::table('pranota_uang_rit_supir_details')->where('id', $detail->id)->update([
                'total_uang_supir' => $updatedTotal,
                'grand_total' => $grandTotal,
                'updated_at' => now(),
            ]);
            DB::table('pranota_uang_rits')->where('no_pranota', self::NO_PRANOTA)->update([
                'uang_rit_supir' => DB::raw('uang_rit_supir - '.self::RIT_AMOUNT),
                'total_rit' => DB::raw('total_rit - '.self::RIT_AMOUNT),
                'total_uang' => DB::raw('total_uang - '.self::RIT_AMOUNT),
                'grand_total_bersih' => DB::raw('grand_total_bersih - '.self::RIT_AMOUNT),
                'updated_at' => now(),
            ]);
        });
    }
};
