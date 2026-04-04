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
        Schema::table('asuransi_tanda_terimas', function (Blueprint $table) {
            $table->string('nomor_urut')->nullable()->after('tanda_terima_lcl_id');
            $table->string('nama_kapal')->nullable()->after('nomor_urut');
            $table->string('nomor_voyage')->nullable()->after('nama_kapal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asuransi_tanda_terimas', function (Blueprint $table) {
            $table->dropColumn(['nomor_urut', 'nama_kapal', 'nomor_voyage']);
        });
    }
};
