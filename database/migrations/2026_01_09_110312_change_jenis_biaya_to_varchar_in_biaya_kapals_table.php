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
        Schema::table('biaya_kapals', function (Blueprint $table) {
            // Drop the index first
            $table->dropIndex(['jenis_biaya']);
            
            // Change ENUM to VARCHAR with foreign key
            $table->string('jenis_biaya', 50)->change();
            
            // Add foreign key constraint
            $table->foreign('jenis_biaya')->references('kode')->on('klasifikasi_biayas')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapals', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['jenis_biaya']);
            
            // Revert back to ENUM (note: this will fail if there's data that doesn't match ENUM values)
            $table->enum('jenis_biaya', ['bahan_bakar', 'pelabuhan', 'perbaikan', 'awak_kapal', 'asuransi', 'lainnya'])->change();
            
            // Recreate the index
            $table->index('jenis_biaya');
        });
    }
};
