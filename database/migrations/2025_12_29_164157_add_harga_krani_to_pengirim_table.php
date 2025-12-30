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
        Schema::table('pengirims', function (Blueprint $table) {
            $table->decimal('harga_krani_20ft', 15, 2)->default(0)->after('catatan');
            $table->decimal('harga_krani_40ft', 15, 2)->default(0)->after('harga_krani_20ft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengirims', function (Blueprint $table) {
            $table->dropColumn(['harga_krani_20ft', 'harga_krani_40ft']);
        });
    }
};
