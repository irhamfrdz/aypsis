<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('nomor_terakhir')->insertOrIgnore([
                'modul' => 'type_gaji_karyawan', 'nomor_terakhir' => 0,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $sequence = DB::table('nomor_terakhir')->where('modul', 'type_gaji_karyawan')->lockForUpdate()->first();
            $next = max((int) $sequence->nomor_terakhir, (int) DB::table('type_gaji_karyawan')->max('type_gaji_id'));
            foreach (DB::table('type_gaji_karyawan')->whereNull('type_gaji_id')->orderBy('id')->pluck('id') as $id) {
                if ($next >= PHP_INT_MAX) {
                    throw new RuntimeException('Rentang Type Gaji ID sudah habis.');
                }
                DB::table('type_gaji_karyawan')->where('id', $id)->update(['type_gaji_id' => ++$next]);
            }
            DB::table('nomor_terakhir')->where('id', $sequence->id)->update(['nomor_terakhir' => $next, 'updated_at' => now()]);
        });
    }

    public function down(): void
    {
        // Assigned identifiers are retained so existing references stay valid.
    }
};
