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
        Schema::table('pembayaran_pranota_uang_jalans', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['pranota_uang_jalan_id']);
            // Then drop the column
            $table->dropColumn('pranota_uang_jalan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_uang_jalans', function (Blueprint $table) {
            $table->foreignId('pranota_uang_jalan_id')
                  ->after('id')
                  ->constrained('pranota_uang_jalans')
                  ->onDelete('cascade');
        });
    }
};
