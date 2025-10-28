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
        Schema::table('prospek', function (Blueprint $table) {
            $table->decimal('total_ton', 12, 3)->nullable()->after('tujuan_pengiriman');
            $table->integer('kuantitas')->nullable()->after('total_ton');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospek', function (Blueprint $table) {
            $table->dropColumn(['total_ton', 'kuantitas']);
        });
    }
};
