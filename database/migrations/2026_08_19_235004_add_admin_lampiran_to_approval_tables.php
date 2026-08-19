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
        Schema::table('absensis', function (Blueprint $table) {
            $table->string('admin_lampiran')->nullable()->after('keterangan');
        });
        
        Schema::table('permohonan_izins', function (Blueprint $table) {
            $table->string('admin_lampiran')->nullable()->after('lampiran');
        });
        
        Schema::table('cutis', function (Blueprint $table) {
            $table->string('admin_lampiran')->nullable()->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn('admin_lampiran');
        });
        
        Schema::table('permohonan_izins', function (Blueprint $table) {
            $table->dropColumn('admin_lampiran');
        });
        
        Schema::table('cutis', function (Blueprint $table) {
            $table->dropColumn('admin_lampiran');
        });
    }
};
