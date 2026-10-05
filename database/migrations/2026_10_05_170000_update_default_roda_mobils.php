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
        // Update Sepeda Motor -> 2 Roda
        DB::table('mobils')
            ->where(function ($q) {
                $q->where('jenis', 'SEPEDA MOTOR')
                    ->orWhere('jenis', 'LIKE', 'MOTOR%');
            })
            ->where(function ($q) {
                $q->whereNull('roda')->orWhere('roda', 0);
            })
            ->update(['roda' => 2]);

        // Update Tractor Head -> 6 Roda
        DB::table('mobils')
            ->where(function ($q) {
                $q->where('jenis', 'TRACTOR HEAD')
                    ->orWhere('jenis', 'TRACKTOR HEAD')
                    ->orWhere('jenis', 'LIKE', '%TRACTOR HEAD%')
                    ->orWhere('jenis', 'LIKE', '%TRACKTOR HEAD%');
            })
            ->where(function ($q) {
                $q->whereNull('roda')->orWhere('roda', 0);
            })
            ->update(['roda' => 6]);

        // Update Buntut 20 Feet -> 8 Roda
        DB::table('mobils')
            ->where(function ($q) {
                $q->where('jenis', 'BUNTUT 20 FEET')
                    ->orWhere('jenis', 'LIKE', '%BUNTUT 20%');
            })
            ->where(function ($q) {
                $q->whereNull('roda')->orWhere('roda', 0);
            })
            ->update(['roda' => 8]);

        // Update Buntut 40 Feet -> 12 Roda
        DB::table('mobils')
            ->where(function ($q) {
                $q->where('jenis', 'BUNTUT 40 FEET')
                    ->orWhere('jenis', 'LIKE', '%BUNTUT 40%');
            })
            ->where(function ($q) {
                $q->whereNull('roda')->orWhere('roda', 0);
            })
            ->update(['roda' => 12]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to revert
    }
};
