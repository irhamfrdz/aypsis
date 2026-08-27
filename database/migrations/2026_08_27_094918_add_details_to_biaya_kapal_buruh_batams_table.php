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
        Schema::table('biaya_kapal_buruh_batams', function (Blueprint $table) {
            $table->string('nomor_bukti')->nullable();
            $table->string('penerima')->nullable();
            $table->string('nama_vendor')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->string('nomor_rekening')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_buruh_batams', function (Blueprint $table) {
            $table->dropColumn(['nomor_bukti', 'penerima', 'nama_vendor', 'bank_id', 'nomor_rekening']);
        });
    }
};
