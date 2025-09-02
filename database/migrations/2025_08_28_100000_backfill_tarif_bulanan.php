<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Backfill existing rows where tarif is NULL or empty string to 'Bulanan'
        \Illuminate\Support\Facades\DB::table('tagihan_kontainer_sewa')
            ->whereNull('tarif')
            ->orWhere('tarif', '')
            ->update(['tarif' => 'Bulanan', 'updated_at' => now()]);
    }

    public function down()
    {
        // cannot reliably revert back to NULL for all rows, so leave as-is
    }
};
