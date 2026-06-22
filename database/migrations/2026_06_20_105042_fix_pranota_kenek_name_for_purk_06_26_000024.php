<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $noPranota = 'PURK-06-26-000024';

        // 1. Update the master record
        $master = DB::table('pranota_uang_rit_keneks')->where('no_pranota', $noPranota)->first();
        if ($master) {
            $oldKenekNama = $master->kenek_nama;
            if (str_contains($oldKenekNama, 'RIKY RISWANTO')) {
                $newKenekNama = str_replace('RIKY RISWANTO', 'WANTO', $oldKenekNama);
                DB::table('pranota_uang_rit_keneks')
                    ->where('no_pranota', $noPranota)
                    ->update(['kenek_nama' => $newKenekNama]);
            }
        }

        // 2. Update the detail record
        DB::table('pranota_uang_rit_kenek_details')
            ->where('no_pranota', $noPranota)
            ->where('kenek_nama', 'RIKY RISWANTO')
            ->update(['kenek_nama' => 'WANTO']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $noPranota = 'PURK-06-26-000024';

        // 1. Revert the master record
        $master = DB::table('pranota_uang_rit_keneks')->where('no_pranota', $noPranota)->first();
        if ($master) {
            $oldKenekNama = $master->kenek_nama;
            if (str_contains($oldKenekNama, 'WANTO')) {
                $newKenekNama = str_replace('WANTO', 'RIKY RISWANTO', $oldKenekNama);
                DB::table('pranota_uang_rit_keneks')
                    ->where('no_pranota', $noPranota)
                    ->update(['kenek_nama' => $newKenekNama]);
            }
        }

        // 2. Revert the detail record
        DB::table('pranota_uang_rit_kenek_details')
            ->where('no_pranota', $noPranota)
            ->where('kenek_nama', 'WANTO')
            ->update(['kenek_nama' => 'RIKY RISWANTO']);
    }
};
