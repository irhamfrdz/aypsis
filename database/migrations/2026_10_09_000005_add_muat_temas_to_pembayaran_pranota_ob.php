<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pranota_ob_muat_temas', function (Blueprint $table) {
            $table->string('status')->default('unpaid');
        });
        Schema::table('pembayaran_pranota_obs', function (Blueprint $table) {
            $table->json('pranota_ob_muat_temas_ids')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_pranota_obs', function (Blueprint $table) {
            $table->dropColumn('pranota_ob_muat_temas_ids');
        });
        Schema::table('pranota_ob_muat_temas', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
