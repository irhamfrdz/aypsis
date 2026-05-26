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
            $table->decimal('adjustment', 15, 2)->default(0)->after('keterangan');
            $table->text('alasan_adjustment')->nullable()->after('adjustment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_ob_antar_gudangs', function (Blueprint $table) {
            $table->dropColumn(['adjustment', 'alasan_adjustment']);
        });
    }
};
