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
        Schema::table('stock_bans', function (Blueprint $table) {
            $table->foreignId('kapal_id')->nullable()->after('penerima_id')->constrained('master_kapals')->onDelete('set null');
            $table->date('tanggal_kirim')->nullable()->after('kapal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_bans', function (Blueprint $table) {
            $table->dropForeign(['kapal_id']);
            $table->dropColumn(['kapal_id', 'tanggal_kirim']);
        });
    }
};
