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
        Schema::table('kontainers', function (Blueprint $table) {
            // Drop unused columns
            $table->dropColumn([
                'harga_satuan',
                'tahun_pembuatan', 
                'keterangan1',
                'keterangan2'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontainers', function (Blueprint $table) {
            // Restore dropped columns
            $table->decimal('harga_satuan', 15, 2)->nullable();
            $table->string('tahun_pembuatan', 4)->nullable();
            $table->text('keterangan1')->nullable();
            $table->text('keterangan2')->nullable();
        });
    }
};
