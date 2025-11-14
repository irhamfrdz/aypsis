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
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->unsignedBigInteger('naik_kapal_id')->nullable()->after('bl_id')->comment('Foreign key ke tabel naik_kapal untuk OB Muat');
            $table->foreign('naik_kapal_id')->references('id')->on('naik_kapal')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->dropForeign(['naik_kapal_id']);
            $table->dropColumn('naik_kapal_id');
        });
    }
};
