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
        Schema::table('biaya_kapal_buruh_bongkars', function (Blueprint $table) {
            $table->decimal('pph_percent', 5, 2)->default(0)->after('notes_adjustment');
            $table->decimal('pph_amount', 15, 2)->default(0)->after('pph_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_buruh_bongkars', function (Blueprint $table) {
            $table->dropColumn(['pph_percent', 'pph_amount']);
        });
    }
};
