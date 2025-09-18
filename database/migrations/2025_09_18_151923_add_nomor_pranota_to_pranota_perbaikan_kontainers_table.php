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
        Schema::table('pranota_perbaikan_kontainers', function (Blueprint $table) {
            $table->string('nomor_pranota')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_perbaikan_kontainers', function (Blueprint $table) {
            $table->dropColumn('nomor_pranota');
        });
    }
};
