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
        Schema::table('biaya_bensin', function (Blueprint $table) {
            $table->string('nomor_rekening')->nullable()->after('keterangan');
            $table->string('penerima_rekening')->nullable()->after('nomor_rekening');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_bensin', function (Blueprint $table) {
            $table->dropColumn(['nomor_rekening', 'penerima_rekening']);
        });
    }
};
