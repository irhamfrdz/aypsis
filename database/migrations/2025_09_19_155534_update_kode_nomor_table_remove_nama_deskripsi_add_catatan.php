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
        Schema::table('kode_nomor', function (Blueprint $table) {
            $table->dropColumn(['nama', 'deskripsi']);
            $table->text('catatan')->nullable()->after('kode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kode_nomor', function (Blueprint $table) {
            $table->dropColumn('catatan');
            $table->string('nama')->after('kode');
            $table->text('deskripsi')->nullable()->after('nama');
        });
    }
};
