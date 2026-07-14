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
        Schema::table('biaya_kapal_perijinan', function (Blueprint $table) {
            $table->date('dari_tanggal')->nullable()->after('no_voyage');
            $table->date('sampai_tanggal')->nullable()->after('dari_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_perijinan', function (Blueprint $table) {
            $table->dropColumn(['dari_tanggal', 'sampai_tanggal']);
        });
    }
};
