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
        Schema::table('biaya_kapal_trucking', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->after('no_bl')->default(0);
            $table->decimal('pph', 15, 2)->after('subtotal')->default(0);
            $table->decimal('total_biaya', 15, 2)->after('pph')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_trucking', function (Blueprint $table) {
            //
        });
    }
};
