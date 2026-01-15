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
        Schema::table('biaya_kapals', function (Blueprint $table) {
            $table->string('nama_vendor', 255)->nullable()->after('penerima');
            $table->string('nomor_rekening', 100)->nullable()->after('nama_vendor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapals', function (Blueprint $table) {
            $table->dropColumn(['nama_vendor', 'nomor_rekening']);
        });
    }
};
