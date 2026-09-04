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
            $table->decimal('jht_biaya', 5, 2)->nullable()->after('biaya_persen');
            $table->decimal('jht_hutang', 5, 2)->nullable()->after('jht_biaya');
            $table->decimal('jkk_tunjangan', 5, 2)->nullable()->after('jht_hutang');
            $table->decimal('jkm_tunjangan', 5, 2)->nullable()->after('jkk_tunjangan');
            $table->decimal('jp_biaya', 5, 2)->nullable()->after('jkm_tunjangan');
            $table->decimal('jp_hutang', 5, 2)->nullable()->after('jp_biaya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_rumus_bpjs', function (Blueprint $table) {
            $table->dropColumn([
                'jht_biaya',
                'jht_hutang',
                'jkk_tunjangan',
                'jkm_tunjangan',
                'jp_biaya',
                'jp_hutang',
            ]);
        });
    }
};
