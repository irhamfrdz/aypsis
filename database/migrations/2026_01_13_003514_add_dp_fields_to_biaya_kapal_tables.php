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
        // Add DP and sisa_pembayaran to biaya_kapals table
        Schema::table('biaya_kapals', function (Blueprint $table) {
            $table->decimal('dp', 15, 2)->default(0)->after('nominal');
            $table->decimal('sisa_pembayaran', 15, 2)->default(0)->after('dp');
        });

        // Add DP and sisa_pembayaran to biaya_kapal_barang table (per kapal)
        Schema::table('biaya_kapal_barang', function (Blueprint $table) {
            $table->decimal('total_nominal', 15, 2)->default(0)->after('subtotal');
            $table->decimal('dp', 15, 2)->default(0)->after('total_nominal');
            $table->decimal('sisa_pembayaran', 15, 2)->default(0)->after('dp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapals', function (Blueprint $table) {
            $table->dropColumn(['dp', 'sisa_pembayaran']);
        });

        Schema::table('biaya_kapal_barang', function (Blueprint $table) {
            $table->dropColumn(['total_nominal', 'dp', 'sisa_pembayaran']);
        });
    }
};
