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
            // Drop foreign key constraint first if it exists
            if (Schema::hasColumn('tanda_terima_lcl', 'jenis_barang_id')) {
                $table->dropForeign(['jenis_barang_id']);
                $table->dropColumn('jenis_barang_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terima_lcl', function (Blueprint $table) {
            // Restore the column if migration is rolled back
            $table->foreignId('jenis_barang_id')->nullable()->after('nama_barang')->constrained('jenis_barangs')->onDelete('cascade');
        });
    }
};
