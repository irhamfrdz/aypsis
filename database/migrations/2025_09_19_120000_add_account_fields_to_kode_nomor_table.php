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
        Schema::table('kode_nomor', function (Blueprint $table) {
            $table->string('nomor_akun')->after('kode')->nullable();
            $table->string('nama_akun')->after('nomor_akun')->nullable();
            $table->string('tipe_akun')->after('nama_akun')->nullable();
            $table->decimal('saldo', 15, 2)->after('tipe_akun')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kode_nomor', function (Blueprint $table) {
            $table->dropColumn(['nomor_akun', 'nama_akun', 'tipe_akun', 'saldo']);
        });
    }
};
