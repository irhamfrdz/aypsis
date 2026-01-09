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
        Schema::table('biaya_kapal_barang', function (Blueprint $table) {
            $table->string('kapal')->nullable()->after('pricelist_buruh_id');
            $table->string('voyage')->nullable()->after('kapal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_barang', function (Blueprint $table) {
            $table->dropColumn(['kapal', 'voyage']);
        });
    }
};
