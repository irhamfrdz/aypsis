<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnal_opp_opts', function (Blueprint $table) {
            $table->string('nama_kapal')->nullable()->after('nomor_referensi');
            $table->string('no_voyage')->nullable()->after('nama_kapal');
            $table->string('nomor_kontainer')->nullable()->after('no_voyage');
            $table->string('nomor_bl')->nullable()->after('nomor_kontainer');
            $table->index(['nama_kapal', 'no_voyage'], 'jurnal_opp_opt_voyage_idx');
        });
    }

    public function down(): void
    {
        Schema::table('jurnal_opp_opts', function (Blueprint $table) {
            $table->dropIndex('jurnal_opp_opt_voyage_idx');
            $table->dropColumn(['nama_kapal', 'no_voyage', 'nomor_kontainer', 'nomor_bl']);
        });
    }
};
