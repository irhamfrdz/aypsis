<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mobils', function (Blueprint $table) {
            $table->date('tanggal_tanda_terima_asset')->nullable();
            $table->string('status_operasional', 100)->nullable();
            $table->string('kondisi_fisik', 255)->nullable();
            $table->string('kategori_asset', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('mobils', function (Blueprint $table) {
            $table->dropColumn(['tanggal_tanda_terima_asset', 'status_operasional', 'kondisi_fisik', 'kategori_asset']);
        });
    }
};
