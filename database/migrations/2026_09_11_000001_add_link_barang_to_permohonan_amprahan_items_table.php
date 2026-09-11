<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('permohonan_amprahan_items') && ! Schema::hasColumn('permohonan_amprahan_items', 'link_barang')) {
            Schema::table('permohonan_amprahan_items', function (Blueprint $table) {
                $table->string('link_barang', 2048)->nullable()->after('nama_barang');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('permohonan_amprahan_items') && Schema::hasColumn('permohonan_amprahan_items', 'link_barang')) {
            Schema::table('permohonan_amprahan_items', function (Blueprint $table) {
                $table->dropColumn('link_barang');
            });
        }
    }
};
