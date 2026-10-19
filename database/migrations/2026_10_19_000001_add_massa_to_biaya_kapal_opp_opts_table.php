<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_opp_opts', function (Blueprint $table) {
            $table->string('massa', 50)->nullable()->after('tanggal_selesai_penumpukan');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_opp_opts', function (Blueprint $table) {
            $table->dropColumn('massa');
        });
    }
};
