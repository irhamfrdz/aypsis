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
        Schema::table('pembayaran_uang_muka', function (Blueprint $table) {
            $table->unsignedBigInteger('mobil_id')->nullable()->after('kegiatan');
            $table->foreign('mobil_id')->references('id')->on('mobils');
            $table->index('mobil_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_uang_muka', function (Blueprint $table) {
            $table->dropForeign(['mobil_id']);
            $table->dropIndex(['mobil_id']);
            $table->dropColumn('mobil_id');
        });
    }
};
