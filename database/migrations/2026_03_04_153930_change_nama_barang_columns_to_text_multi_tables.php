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
        if (Schema::hasTable('tanda_terima_tanpa_surat_jalan')) {
            Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
                $table->text('nama_barang')->nullable()->change();
            });
        }

        if (Schema::hasTable('tanda_terimas_lcl')) {
            Schema::table('tanda_terimas_lcl', function (Blueprint $table) {
                $table->text('nama_barang')->nullable()->change();
            });
        }

        if (Schema::hasTable('tanda_terima_lcl_items')) {
            Schema::table('tanda_terima_lcl_items', function (Blueprint $table) {
                $table->text('nama_barang')->nullable()->change();
            });
        }

        if (Schema::hasTable('prospek')) {
            Schema::table('prospek', function (Blueprint $table) {
                $table->text('barang')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tanda_terima_tanpa_surat_jalan')) {
            Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
                $table->string('nama_barang', 255)->nullable()->change();
            });
        }

        if (Schema::hasTable('tanda_terimas_lcl')) {
            Schema::table('tanda_terimas_lcl', function (Blueprint $table) {
                $table->string('nama_barang', 255)->nullable()->change();
            });
        }

        if (Schema::hasTable('tanda_terima_lcl_items')) {
            Schema::table('tanda_terima_lcl_items', function (Blueprint $table) {
                $table->string('nama_barang', 255)->nullable()->change();
            });
        }

        if (Schema::hasTable('prospek')) {
            Schema::table('prospek', function (Blueprint $table) {
                $table->string('barang', 255)->nullable()->change();
            });
        }
    }
};
