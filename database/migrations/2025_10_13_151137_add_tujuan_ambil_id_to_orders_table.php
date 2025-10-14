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
        Schema::table('orders', function (Blueprint $table) {
            // Add foreign key to tujuan_kegiatan_utamas table
            $table->foreignId('tujuan_ambil_id')->nullable()->after('tujuan_ambil')->constrained('tujuan_kegiatan_utamas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['tujuan_ambil_id']);
            $table->dropColumn('tujuan_ambil_id');
        });
    }
};
