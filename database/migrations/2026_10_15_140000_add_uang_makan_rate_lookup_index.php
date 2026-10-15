<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uang_makans', function (Blueprint $table) {
            $table->index(['karyawan_id', 'tanggal', 'id'], 'uang_makans_karyawan_tanggal_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('uang_makans', function (Blueprint $table) {
            $table->dropIndex('uang_makans_karyawan_tanggal_id_idx');
        });
    }
};
