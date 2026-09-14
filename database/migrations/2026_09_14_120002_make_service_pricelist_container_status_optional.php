<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_pricelist_ob_antar_gudang', function (Blueprint $table) {
            $table->enum('status_kontainer', ['full', 'empty'])->nullable()->change();
        });

        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->enum('status_kontainer', ['full', 'empty'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->enum('status_kontainer', ['full', 'empty'])->nullable(false)->change();
        });

        Schema::table('master_pricelist_ob_antar_gudang', function (Blueprint $table) {
            $table->enum('status_kontainer', ['full', 'empty'])->nullable(false)->change();
        });
    }
};
