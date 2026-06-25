<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('karyawan_tidak_tetap_id')->nullable()->after('karyawan_id');
            $table->foreign('karyawan_tidak_tetap_id')->references('id')->on('karyawan_tidak_tetaps')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['karyawan_tidak_tetap_id']);
            $table->dropColumn('karyawan_tidak_tetap_id');
        });
    }
};
