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
        Schema::table('pranota_puml_potongans', function (Blueprint $table) {
            $table->dropForeign(['karyawan_id']);
            $table->string('tipe_karyawan')->default('App\\\\Models\\\\Karyawan')->after('pranota_puml_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_puml_potongans', function (Blueprint $table) {
            $table->dropColumn('tipe_karyawan');
            $table->foreign('karyawan_id')->references('id')->on('karyawans')->onDelete('cascade');
        });
    }
};
