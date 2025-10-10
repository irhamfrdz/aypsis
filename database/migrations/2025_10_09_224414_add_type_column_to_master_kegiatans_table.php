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
        Schema::table('master_kegiatans', function (Blueprint $table) {
            $table->string('type', 50)->nullable()->after('nama_kegiatan')->comment('Tipe/jenis kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_kegiatans', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
