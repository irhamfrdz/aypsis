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
        Schema::table('manifests', function (Blueprint $table) {
            $table->text('alamat_pengirim')->nullable()->after('pengirim');
            $table->text('alamat_penerima')->nullable()->after('penerima');
            $table->string('pelabuhan_muat', 255)->nullable()->after('nama_kapal');
            $table->string('pelabuhan_bongkar', 255)->nullable()->after('pelabuhan_muat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->dropColumn(['alamat_pengirim', 'alamat_penerima', 'pelabuhan_muat', 'pelabuhan_bongkar']);
        });
    }
};
