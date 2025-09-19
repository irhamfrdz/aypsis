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
        Schema::table('akun_coa', function (Blueprint $table) {
            $table->string('kode_nomor')->nullable()->after('nomor_akun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('akun_coa', function (Blueprint $table) {
            $table->dropColumn('kode_nomor');
        });
    }
};
