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
            $table->unsignedBigInteger('penerima_id')->nullable()->after('mobil_id');
            $table->foreign('penerima_id')->references('id')->on('karyawans');
            $table->index('penerima_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_uang_muka', function (Blueprint $table) {
            $table->dropForeign(['penerima_id']);
            $table->dropIndex(['penerima_id']);
            $table->dropColumn('penerima_id');
        });
    }
};
