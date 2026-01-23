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
            $table->decimal('pph', 15, 2)->default(0)->after('total_nominal');
            $table->decimal('grand_total', 15, 2)->default(0)->after('pph');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_tkbm', function (Blueprint $table) {
            $table->dropColumn(['pph', 'grand_total']);
        });
    }
};
