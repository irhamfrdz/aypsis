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
        Schema::table('tanda_terima_lcl', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['tujuan_pengiriman_id']);
            
            // Add the correct foreign key constraint to master_tujuan_kirim
            $table->foreign('tujuan_pengiriman_id')->references('id')->on('master_tujuan_kirim')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terima_lcl', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['tujuan_pengiriman_id']);
            
            // Restore the old foreign key constraint (for rollback purposes)
            $table->foreign('tujuan_pengiriman_id')->references('id')->on('tujuan_kegiatan_utamas')->onDelete('cascade');
        });
    }
};
