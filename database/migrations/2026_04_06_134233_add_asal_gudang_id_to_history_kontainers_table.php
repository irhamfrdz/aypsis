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
        Schema::table('history_kontainers', function (Blueprint $table) {
            $table->unsignedBigInteger('asal_gudang_id')->nullable()->after('tanggal_kegiatan');
            $table->foreign('asal_gudang_id')->references('id')->on('gudangs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history_kontainers', function (Blueprint $table) {
            $table->dropForeign(['asal_gudang_id']);
            $table->dropColumn('asal_gudang_id');
        });
    }
};
