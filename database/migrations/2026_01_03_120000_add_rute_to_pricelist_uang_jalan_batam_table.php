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
        if (!Schema::hasColumn('pricelist_uang_jalan_batam', 'rute')) {
            Schema::table('pricelist_uang_jalan_batam', function (Blueprint $table) {
                $table->string('rute')->nullable()->after('ring')->comment('Rute pengiriman');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pricelist_uang_jalan_batam', 'rute')) {
            Schema::table('pricelist_uang_jalan_batam', function (Blueprint $table) {
                $table->dropColumn('rute');
            });
        }
    }
};
