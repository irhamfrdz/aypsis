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
        Schema::table('naik_kapal', function (Blueprint $table) {
            $table->string('jenis_barang')->nullable()->after('nomor_kontainer');
            $table->string('tipe_kontainer_detail')->nullable()->after('tipe_kontainer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('naik_kapal', function (Blueprint $table) {
            $table->dropColumn(['jenis_barang', 'tipe_kontainer_detail']);
        });
    }
};
