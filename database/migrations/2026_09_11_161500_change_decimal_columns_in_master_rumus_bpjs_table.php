<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('master_rumus_bpjs', function (Blueprint $table) {
            $table->decimal('tunjangan_persen', 15, 2)->nullable()->change();
            $table->decimal('hutang_persen', 15, 2)->nullable()->change();
            $table->decimal('biaya_persen', 15, 2)->nullable()->change();
            $table->decimal('jht_biaya', 15, 2)->nullable()->change();
            $table->decimal('jht_hutang', 15, 2)->nullable()->change();
            $table->decimal('jkk_tunjangan', 15, 2)->nullable()->change();
            $table->decimal('jkm_tunjangan', 15, 2)->nullable()->change();
            $table->decimal('jp_biaya', 15, 2)->nullable()->change();
            $table->decimal('jp_hutang', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_rumus_bpjs', function (Blueprint $table) {
            $table->decimal('tunjangan_persen', 5, 2)->nullable()->change();
            $table->decimal('hutang_persen', 5, 2)->nullable()->change();
            $table->decimal('biaya_persen', 5, 2)->nullable()->change();
            $table->decimal('jht_biaya', 5, 2)->nullable()->change();
            $table->decimal('jht_hutang', 5, 2)->nullable()->change();
            $table->decimal('jkk_tunjangan', 5, 2)->nullable()->change();
            $table->decimal('jkm_tunjangan', 5, 2)->nullable()->change();
            $table->decimal('jp_biaya', 5, 2)->nullable()->change();
            $table->decimal('jp_hutang', 5, 2)->nullable()->change();
        });
    }
};
