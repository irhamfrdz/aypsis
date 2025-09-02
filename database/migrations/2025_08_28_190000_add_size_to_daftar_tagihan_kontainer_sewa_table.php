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
        if (Schema::hasTable('daftar_tagihan_kontainer_sewa') && !Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'size')) {
            Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
                $table->string('size')->nullable()->after('nomor_kontainer');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('daftar_tagihan_kontainer_sewa') && Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'size')) {
            Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
                $table->dropColumn('size');
            });
        }
    }
};
