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
        Schema::table('uang_jalans', function (Blueprint $table) {
            if (Schema::hasColumn('uang_jalans', 'kategori_uang_jalan')) {
                $table->dropColumn('kategori_uang_jalan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uang_jalans', function (Blueprint $table) {
            $table->enum('kategori_uang_jalan', ['uang_jalan', 'non_uang_jalan'])->nullable()->after('jenis_transaksi');
        });
    }
};
