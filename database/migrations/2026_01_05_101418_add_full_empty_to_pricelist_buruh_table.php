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
        Schema::table('pricelist_buruh', function (Blueprint $table) {
            $table->decimal('full', 15, 2)->default(0)->after('tarif');
            $table->decimal('empty', 15, 2)->default(0)->after('full');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelist_buruh', function (Blueprint $table) {
            $table->dropColumn(['full', 'empty']);
        });
    }
};
