<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pranota_tagihan_lain_lain_batams', function (Blueprint $table) {
            $table->string('bank', 100)->nullable();
            $table->string('nomor_rekening', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pranota_tagihan_lain_lain_batams', function (Blueprint $table) {
            $table->dropColumn(['bank', 'nomor_rekening']);
        });
    }
};
