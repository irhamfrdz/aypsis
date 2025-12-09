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
        Schema::table('pembayaran_aktivitas_lains', function (Blueprint $table) {
            $table->string('jenis_penyesuaian')->nullable()->after('jenis_aktivitas');
            $table->json('tipe_penyesuaian')->nullable()->after('jenis_penyesuaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_aktivitas_lains', function (Blueprint $table) {
            $table->dropColumn(['jenis_penyesuaian', 'tipe_penyesuaian']);
        });
    }
};
