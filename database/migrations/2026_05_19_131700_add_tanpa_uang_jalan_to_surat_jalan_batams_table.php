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
        Schema::table('surat_jalan_batams', function (Blueprint $table) {
            $table->boolean('tanpa_uang_jalan')->default(0)->after('uang_jalan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_batams', function (Blueprint $table) {
            $table->dropColumn('tanpa_uang_jalan');
        });
    }
};
