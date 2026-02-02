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
            $table->enum('status_masak', ['belum', 'sedang', 'sudah'])->default('belum')->after('status');
            $table->integer('jumlah_masak')->default(0)->after('status_masak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_bans', function (Blueprint $table) {
            $table->dropColumn(['status_masak', 'jumlah_masak']);
        });
    }
};
