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
        Schema::table('biaya_kapal_stuffing', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->default(0)->after('tanda_terima_ids');
            $table->decimal('pph', 15, 2)->default(0)->after('subtotal');
            $table->decimal('total_biaya', 15, 2)->default(0)->after('pph');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_stuffing', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'pph', 'total_biaya']);
        });
    }
};
