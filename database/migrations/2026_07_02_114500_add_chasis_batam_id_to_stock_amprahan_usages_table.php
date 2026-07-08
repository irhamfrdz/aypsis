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
        Schema::table('stock_amprahan_usages', function (Blueprint $table) {
            $table->foreignId('chasis_batam_id')
                ->nullable()
                ->after('buntut_id')
                ->constrained('master_chasis_batams')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_amprahan_usages', function (Blueprint $table) {
            $table->dropForeign(['chasis_batam_id']);
            $table->dropColumn('chasis_batam_id');
        });
    }
};
