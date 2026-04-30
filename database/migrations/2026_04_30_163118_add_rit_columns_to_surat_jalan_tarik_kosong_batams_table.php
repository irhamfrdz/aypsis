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
        Schema::table('surat_jalan_tarik_kosong_batams', function (Blueprint $table) {
            $table->string('rit')->nullable()->after('f_e');
            $table->string('status_pembayaran_uang_rit')->nullable()->after('status_pembayaran_uang_jalan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_tarik_kosong_batams', function (Blueprint $table) {
            $table->dropColumn(['rit', 'status_pembayaran_uang_rit']);
        });
    }
};
