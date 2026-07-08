<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pricelist_uang_jalan_batam', function (Blueprint $table) {
            if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
                $table->dropIndex('pricelist_uang_jalan_batam_expedisi_ring_size_f_e_index');
            }
            $table->dropColumn('f_e');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelist_uang_jalan_batam', function (Blueprint $table) {
            $table->enum('f_e', ['Full', 'Empty'])->after('size');
        });
    }
};
