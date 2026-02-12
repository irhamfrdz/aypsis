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
        Schema::table('master_pricelist_kanisir_bans', function (Blueprint $table) {
            $table->decimal('harga_900_benang', 15, 2)->after('harga_900_kawat')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pricelist_kanisir_bans', function (Blueprint $table) {
            $table->dropColumn('harga_900_benang');
        });
    }
};
