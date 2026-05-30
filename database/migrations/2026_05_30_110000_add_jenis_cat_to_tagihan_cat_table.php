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
        Schema::table('tagihan_cat', function (Blueprint $table) {
            if (! Schema::hasColumn('tagihan_cat', 'jenis_cat')) {
                $table->string('jenis_cat')->nullable()->after('nomor_kontainer');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_cat', function (Blueprint $table) {
            if (Schema::hasColumn('tagihan_cat', 'jenis_cat')) {
                $table->dropColumn('jenis_cat');
            }
        });
    }
};
