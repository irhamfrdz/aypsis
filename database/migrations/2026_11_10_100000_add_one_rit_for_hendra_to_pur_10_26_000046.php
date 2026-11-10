<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const NO_PRANOTA = 'PUR-10-26-000046';

    public function up(): void
    {
        DB::transaction(function (): void {
            $detail = DB::table('pranota_uang_rit_supir_details')
                ->where('no_pranota', self::NO_PRANOTA)
                ->whereRaw('LOWER(TRIM(supir_nama)) = ?', ['hendra'])
                ->lockForUpdate()
                ->first();

            if (! $detail || (int) $detail->jumlah_rit !== 7) {
                return;
            }

            DB::table('pranota_uang_rit_supir_details')
                ->where('id', $detail->id)
                ->update([
                    'jumlah_rit' => 8,
                    'updated_at' => now(),
                ]);
        });
    }

    public function down(): void
    {
        DB::table('pranota_uang_rit_supir_details')
            ->where('no_pranota', self::NO_PRANOTA)
            ->whereRaw('LOWER(TRIM(supir_nama)) = ?', ['hendra'])
            ->where('jumlah_rit', 8)
            ->update([
                'jumlah_rit' => 7,
                'updated_at' => now(),
            ]);
    }
};
