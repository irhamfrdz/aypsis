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
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('jumlah')->nullable()->after('tipe_kontainer');
            $table->decimal('tonase', 15, 3)->nullable()->after('jumlah');
            $table->decimal('meter_kubik', 15, 3)->nullable()->after('tonase');
            $table->json('dimensi_items')->nullable()->after('meter_kubik');
            $table->json('nama_barang')->nullable()->after('dimensi_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['jumlah', 'tonase', 'meter_kubik', 'dimensi_items', 'nama_barang']);
        });
    }
};
