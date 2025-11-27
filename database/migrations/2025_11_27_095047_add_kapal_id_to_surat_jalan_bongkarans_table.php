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
        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            $table->unsignedBigInteger('kapal_id')->nullable()->after('aktifitas');
            $table->foreign('kapal_id')->references('id')->on('master_kapals')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            $table->dropForeign(['kapal_id']);
            $table->dropColumn('kapal_id');
        });
    }
};
