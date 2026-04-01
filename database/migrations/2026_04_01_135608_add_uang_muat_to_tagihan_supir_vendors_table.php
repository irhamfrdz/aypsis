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
        Schema::table('tagihan_supir_vendors', function (Blueprint $table) {
            $table->decimal('uang_muat', 15, 2)->default(0)->after('nominal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_supir_vendors', function (Blueprint $table) {
            $table->dropColumn('uang_muat');
        });
    }
};
