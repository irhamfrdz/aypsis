<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_kartu_bensin_batam_histories', function (Blueprint $table) {
            $table->string('nomor_pranota', 40)->nullable()->unique();
            $table->decimal('biaya_admin', 15, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('master_kartu_bensin_batam_histories', function (Blueprint $table) {
            $table->dropUnique(['nomor_pranota']);
            $table->dropColumn(['nomor_pranota', 'biaya_admin']);
        });
    }
};
