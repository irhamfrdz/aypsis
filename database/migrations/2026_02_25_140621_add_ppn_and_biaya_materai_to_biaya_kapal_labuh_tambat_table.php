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
        Schema::table('biaya_kapal_labuh_tambat', function (Blueprint $table) {
            $table->decimal('ppn', 15, 2)->default(0)->after('sub_total');
            $table->decimal('biaya_materai', 15, 2)->default(0)->after('ppn');
            if (Schema::hasColumn('biaya_kapal_labuh_tambat', 'pph')) {
                $table->dropColumn('pph');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_labuh_tambat', function (Blueprint $table) {
            $table->decimal('pph', 15, 2)->default(0)->after('sub_total');
            $table->dropColumn(['ppn', 'biaya_materai']);
        });
    }
};
