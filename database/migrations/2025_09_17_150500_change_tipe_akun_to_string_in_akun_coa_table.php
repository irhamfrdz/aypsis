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
            // Change enum column to string
            $table->string('tipe_akun', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('akun_coa', function (Blueprint $table) {
            // Change back to enum
            $table->enum('tipe_akun', ['Aset', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban'])->change();
        });
    }
};
