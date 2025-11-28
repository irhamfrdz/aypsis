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
            $table->unsignedBigInteger('bl_id')->nullable()->after('no_bl')->index();
            $table->foreign('bl_id')->references('id')->on('bls')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            $table->dropForeign(['bl_id']);
            $table->dropIndex(['bl_id']);
            $table->dropColumn('bl_id');
        });
    }
};
