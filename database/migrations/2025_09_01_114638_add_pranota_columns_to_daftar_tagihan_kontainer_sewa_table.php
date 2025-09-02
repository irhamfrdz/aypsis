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
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->string('status_pranota')->nullable()->after('status'); // null, included, processed
            $table->unsignedBigInteger('pranota_id')->nullable()->after('status_pranota');
            $table->index(['status_pranota', 'pranota_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->dropIndex(['status_pranota', 'pranota_id']);
            $table->dropColumn(['status_pranota', 'pranota_id']);
        });
    }
};
