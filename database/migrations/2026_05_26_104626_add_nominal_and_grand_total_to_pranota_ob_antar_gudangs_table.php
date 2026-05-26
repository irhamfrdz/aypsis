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
        Schema::table('pranota_ob_antar_gudangs', function (Blueprint $table) {
            $table->decimal('nominal', 15, 2)->default(0)->after('keterangan');
            $table->decimal('grand_total', 15, 2)->default(0)->after('alasan_adjustment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_ob_antar_gudangs', function (Blueprint $table) {
            $table->dropColumn(['nominal', 'grand_total']);
        });
    }
};
