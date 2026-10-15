<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_opp_opts', function (Blueprint $table) {
            $table->date('tanggal_mulai_penumpukan')->nullable()->after('catatan');
            $table->date('tanggal_selesai_penumpukan')->nullable()->after('tanggal_mulai_penumpukan');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_opp_opts', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai_penumpukan', 'tanggal_selesai_penumpukan']);
        });
    }
};
