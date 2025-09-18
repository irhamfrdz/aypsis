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
        Schema::table('pajaks', function (Blueprint $table) {
            // Rename kolom nama menjadi nama_status
            $table->renameColumn('nama', 'nama_status');

            // Hapus kolom status dan jalak
            $table->dropColumn(['status', 'jalak']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pajaks', function (Blueprint $table) {
            // Tambahkan kembali kolom status dan jalak
            $table->boolean('status')->default(true);
            $table->decimal('jalak', 10, 2)->nullable();

            // Rename kolom nama_status kembali menjadi nama
            $table->renameColumn('nama_status', 'nama');
        });
    }
};
