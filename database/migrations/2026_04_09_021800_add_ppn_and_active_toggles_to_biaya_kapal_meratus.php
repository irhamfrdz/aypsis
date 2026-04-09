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
        Schema::table('biaya_kapal_meratus', function (Blueprint $table) {
            $table->decimal('ppn', 15, 2)->default(0)->after('pph');
            $table->boolean('pph_active')->default(true)->after('ppn');
            $table->boolean('ppn_active')->default(false)->after('pph_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_meratus', function (Blueprint $table) {
            $table->dropColumn(['ppn', 'pph_active', 'ppn_active']);
        });
    }
};
