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
            $table->string('nomor_seal')->nullable()->after('size_kontainer');
            $table->date('tanggal_seal')->nullable()->after('nomor_seal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terima_lcl', function (Blueprint $table) {
            $table->dropColumn(['nomor_seal', 'tanggal_seal']);
        });
    }
};
