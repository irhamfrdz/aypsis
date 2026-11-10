<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengisian_tanggal_dokumens', function (Blueprint $table) {
            $table->date('tanggal_keluar_gate_tujuan')->nullable();
            $table->date('tanggal_keluar_gudang_ayp')->nullable();
            $table->date('tanggal_tiba_gudang_customer')->nullable();
            $table->date('tanggal_keluar_gudang_customer')->nullable();
            $table->date('tanggal_masuk_gate_tujuan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pengisian_tanggal_dokumens', function (Blueprint $table) {
            $table->dropColumn(['tanggal_keluar_gate_tujuan','tanggal_keluar_gudang_ayp','tanggal_tiba_gudang_customer','tanggal_keluar_gudang_customer','tanggal_masuk_gate_tujuan']);
        });
    }
};
