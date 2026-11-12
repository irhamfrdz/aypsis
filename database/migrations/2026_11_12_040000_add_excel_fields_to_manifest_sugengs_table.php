<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manifest_sugengs', function (Blueprint $table) {
            $table->string('nama_kapal')->nullable()->index();
            $table->string('no_voyage')->nullable()->index();
            $table->string('nomor_bl')->nullable();
            $table->string('no_seal')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('size_kontainer')->nullable();
            $table->json('perincian_barang')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('manifest_sugengs', function (Blueprint $table) {
            $table->dropIndex(['nama_kapal']);
            $table->dropIndex(['no_voyage']);
            $table->dropColumn(['nama_kapal', 'no_voyage', 'nomor_bl', 'no_seal', 'tipe_kontainer', 'size_kontainer', 'perincian_barang']);
        });
    }
};
