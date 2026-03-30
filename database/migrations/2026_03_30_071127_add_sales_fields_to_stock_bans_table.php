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
        Schema::table('stock_bans', function (Blueprint $table) {
            $table->decimal('harga_jual', 15, 2)->nullable()->after('harga_beli');
            $table->string('pembeli')->nullable()->after('harga_jual');
            $table->date('tanggal_jual')->nullable()->after('pembeli');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_bans', function (Blueprint $table) {
            $table->dropColumn(['harga_jual', 'pembeli', 'tanggal_jual']);
        });
    }
};
