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
        Schema::table('tagihan_pelindos', function (Blueprint $table) {
            $table->string('kapal')->nullable()->after('tanggal_tagihan');
            $table->string('voyage')->nullable()->after('kapal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_pelindos', function (Blueprint $table) {
            $table->dropColumn(['kapal', 'voyage']);
        });
    }
};
