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
        if (Schema::hasTable('tanda_terimas_lcl') && ! Schema::hasColumn('tanda_terimas_lcl', 'ukuran')) {
            Schema::table('tanda_terimas_lcl', function (Blueprint $table) {
                $table->string('ukuran')->nullable()->after('nama_pengirim');
            });
        }

        if (Schema::hasTable('tanda_terima_lcl_items') && ! Schema::hasColumn('tanda_terima_lcl_items', 'ukuran')) {
            Schema::table('tanda_terima_lcl_items', function (Blueprint $table) {
                $table->string('ukuran')->nullable()->after('nama_barang');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tanda_terimas_lcl') && Schema::hasColumn('tanda_terimas_lcl', 'ukuran')) {
            Schema::table('tanda_terimas_lcl', function (Blueprint $table) {
                $table->dropColumn('ukuran');
            });
        }

        if (Schema::hasTable('tanda_terima_lcl_items') && Schema::hasColumn('tanda_terima_lcl_items', 'ukuran')) {
            Schema::table('tanda_terima_lcl_items', function (Blueprint $table) {
                $table->dropColumn('ukuran');
            });
        }
    }
};
