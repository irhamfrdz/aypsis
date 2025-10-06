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
        Schema::table('master_kegiatans', function (Blueprint $table) {
            $table->dropColumn('uang_jalan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_kegiatans', function (Blueprint $table) {
            $table->decimal('uang_jalan', 15, 2)->nullable()->default(0)->after('keterangan');
        });
    }
};
