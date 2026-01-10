<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check and drop indexes if they exist
        $indexes = DB::select("SHOW INDEX FROM pranota_uang_rits WHERE Column_name IN ('no_surat_jalan', 'supir_nama')");
        
        foreach ($indexes as $index) {
            if ($index->Column_name === 'no_surat_jalan' && $index->Key_name !== 'PRIMARY') {
                DB::statement("ALTER TABLE pranota_uang_rits DROP INDEX `{$index->Key_name}`");
            }
            if ($index->Column_name === 'supir_nama' && $index->Key_name !== 'PRIMARY') {
                DB::statement("ALTER TABLE pranota_uang_rits DROP INDEX `{$index->Key_name}`");
            }
        }
        
        Schema::table('pranota_uang_rits', function (Blueprint $table) {
            // Change no_surat_jalan from VARCHAR(255) to TEXT to accommodate long lists
            $table->text('no_surat_jalan')->nullable()->change();
            
            // Also change supir_nama and kenek_nama to TEXT for consistency
            $table->text('supir_nama')->nullable()->change();
            $table->text('kenek_nama')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_uang_rits', function (Blueprint $table) {
            // Revert back to VARCHAR(255)
            $table->string('no_surat_jalan', 255)->nullable()->change();
            $table->string('supir_nama', 255)->nullable()->change();
            $table->string('kenek_nama', 255)->nullable()->change();
        });
        
        Schema::table('pranota_uang_rits', function (Blueprint $table) {
            // Re-create indexes
            $table->index('no_surat_jalan');
            $table->index('supir_nama');
        });
    }
};
