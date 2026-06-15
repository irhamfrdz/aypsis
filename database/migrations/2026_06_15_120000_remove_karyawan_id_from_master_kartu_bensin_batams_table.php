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
        Schema::table('master_kartu_bensin_batams', function (Blueprint $table) {
            $table->dropForeign(['karyawan_id']);
            $table->dropColumn('karyawan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_kartu_bensin_batams', function (Blueprint $table) {
            $table->unsignedBigInteger('karyawan_id')->nullable()->after('mobil_id');
            $table->foreign('karyawan_id')->references('id')->on('karyawans')->onDelete('set null');
        });
    }
};
