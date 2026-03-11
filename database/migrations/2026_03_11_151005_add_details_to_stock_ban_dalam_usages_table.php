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
        Schema::table('stock_ban_dalam_usages', function (Blueprint $table) {
            $table->unsignedBigInteger('mobil_id')->nullable()->change();
            $table->foreignId('penerima_id')->nullable()->after('mobil_id')->constrained('karyawans')->onDelete('set null');
            $table->foreignId('kapal_id')->nullable()->after('penerima_id')->constrained('master_kapals')->onDelete('set null');
            $table->foreignId('gudang_id')->nullable()->after('kapal_id')->constrained('master_gudang_bans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_ban_dalam_usages', function (Blueprint $table) {
            $table->dropForeign(['penerima_id']);
            $table->dropForeign(['kapal_id']);
            $table->dropForeign(['gudang_id']);
            $table->dropColumn(['penerima_id', 'kapal_id', 'gudang_id']);
            $table->unsignedBigInteger('mobil_id')->change();
        });
    }
};
