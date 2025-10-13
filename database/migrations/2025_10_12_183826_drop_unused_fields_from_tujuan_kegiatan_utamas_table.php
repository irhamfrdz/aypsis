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
        Schema::table('tujuan_kegiatan_utamas', function (Blueprint $table) {
            // Drop unused fields from original table structure
            $table->dropColumn(['nama', 'deskripsi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tujuan_kegiatan_utamas', function (Blueprint $table) {
            // Restore fields if needed to rollback
            $table->string('nama')->nullable()->after('id');
            $table->text('deskripsi')->nullable()->after('nama');
        });
    }
};
