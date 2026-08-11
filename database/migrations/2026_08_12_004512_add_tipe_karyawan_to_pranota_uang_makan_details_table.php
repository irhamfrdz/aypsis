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
        Schema::table('pranota_uang_makan_details', function (Blueprint $table) {
            $table->string('tipe_karyawan')->default('App\\\\Models\\\\Karyawan')->after('pranota_uang_makan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_uang_makan_details', function (Blueprint $table) {
            $table->dropColumn('tipe_karyawan');
        });
    }
};
