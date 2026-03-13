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
        Schema::table('biaya_kapal_tkbm', function (Blueprint $table) {
            $table->decimal('total_sebelum_adjustment', 15, 2)->nullable()->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_tkbm', function (Blueprint $table) {
            $table->dropColumn('total_sebelum_adjustment');
        });
    }
};
