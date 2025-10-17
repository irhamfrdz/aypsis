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
        Schema::table('tanda_terimas', function (Blueprint $table) {
            // Add jenis_barang after kegiatan
            $table->string('jenis_barang')->nullable()->after('kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->dropColumn('jenis_barang');
        });
    }
};
