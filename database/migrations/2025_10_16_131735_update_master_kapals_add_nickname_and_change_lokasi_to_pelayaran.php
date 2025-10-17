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
        Schema::table('master_kapals', function (Blueprint $table) {
            // Add nickname field
            $table->string('nickname')->nullable()->after('nama_kapal');

            // Rename lokasi to pelayaran (pemilik kapal)
            $table->renameColumn('lokasi', 'pelayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_kapals', function (Blueprint $table) {
            // Remove nickname field
            $table->dropColumn('nickname');

            // Rename back pelayaran to lokasi
            $table->renameColumn('pelayaran', 'lokasi');
        });
    }
};
