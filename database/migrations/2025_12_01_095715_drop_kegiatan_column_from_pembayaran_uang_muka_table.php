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
        Schema::table('pembayaran_uang_muka', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['kegiatan']);
            
            // Drop index if exists
            $table->dropIndex(['kegiatan']);
            
            // Drop the column
            $table->dropColumn('kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_uang_muka', function (Blueprint $table) {
            // Re-add the column as unsigned big integer
            $table->unsignedBigInteger('kegiatan')->after('jenis_transaksi');
            
            // Add index
            $table->index('kegiatan');
            
            // Add foreign key constraint
            $table->foreign('kegiatan')->references('id')->on('master_kegiatans');
        });
    }
};
