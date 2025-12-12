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
        // Add columns to bls table
        Schema::table('bls', function (Blueprint $table) {
            $table->string('asal_kontainer')->nullable()->after('nama_barang');
            $table->string('ke')->nullable()->after('asal_kontainer');
        });

        // Add columns to naik_kapal table
        Schema::table('naik_kapal', function (Blueprint $table) {
            $table->string('asal_kontainer')->nullable()->after('jenis_barang');
            $table->string('ke')->nullable()->after('asal_kontainer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bls', function (Blueprint $table) {
            $table->dropColumn(['asal_kontainer', 'ke']);
        });

        Schema::table('naik_kapal', function (Blueprint $table) {
            $table->dropColumn(['asal_kontainer', 'ke']);
        });
    }
};
