<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawan_tidak_tetaps', function (Blueprint $table) {
            $table->date('tanggal_berhenti')->nullable()->after('tanggal_masuk');
        });
    }

    public function down(): void
    {
        Schema::table('karyawan_tidak_tetaps', function (Blueprint $table) {
            $table->dropColumn('tanggal_berhenti');
        });
    }
};
