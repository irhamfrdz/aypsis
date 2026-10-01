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
        Schema::table('surat_jalan_bongkaran_batams', function (Blueprint $table) {
            if (! Schema::hasColumn('surat_jalan_bongkaran_batams', 'menggunakan_lolo')) {
                $table->boolean('menggunakan_lolo')->default(false)->after('lanjut_muat');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_bongkaran_batams', function (Blueprint $table) {
            if (Schema::hasColumn('surat_jalan_bongkaran_batams', 'menggunakan_lolo')) {
                $table->dropColumn('menggunakan_lolo');
            }
        });
    }
};
