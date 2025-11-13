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
        Schema::table('bls', function (Blueprint $table) {
            $table->enum('status_bongkar', ['Sudah Bongkar', 'Belum Bongkar'])
                  ->default('Belum Bongkar')
                  ->after('id'); // Sesuaikan posisi kolom sesuai kebutuhan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bls', function (Blueprint $table) {
            $table->dropColumn('status_bongkar');
        });
    }
};
