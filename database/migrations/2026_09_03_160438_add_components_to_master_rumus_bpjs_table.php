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
            // Make old columns nullable
            $table->enum('tipe_rumus', ['nominal', 'persentase'])->nullable()->change();
            $table->decimal('nilai', 15, 2)->nullable()->change();
            
            // Add new components
            $table->decimal('tunjangan_persen', 5, 2)->nullable()->after('group_name');
            $table->decimal('hutang_persen', 5, 2)->nullable()->after('tunjangan_persen');
            $table->decimal('biaya_persen', 5, 2)->nullable()->after('hutang_persen');
            $table->string('keterangan_custom')->nullable()->after('biaya_persen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_rumus_bpjs', function (Blueprint $table) {
            $table->enum('tipe_rumus', ['nominal', 'persentase'])->nullable(false)->change();
            $table->decimal('nilai', 15, 2)->nullable(false)->change();
            
            $table->dropColumn(['tunjangan_persen', 'hutang_persen', 'biaya_persen', 'keterangan_custom']);
        });
    }
};
