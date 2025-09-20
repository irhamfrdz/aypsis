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
        Schema::table('tagihan_cat', function (Blueprint $table) {
            // Rename tanggal_tagihan to tanggal_cat
            $table->renameColumn('tanggal_tagihan', 'tanggal_cat');

            // Add nomor_tagihan_cat column
            $table->string('nomor_tagihan_cat')->nullable()->after('id');

            // Drop jumlah column
            $table->dropColumn('jumlah');

            // Drop perbaikan_kontainer_id column
            $table->dropForeign(['perbaikan_kontainer_id']);
            $table->dropColumn('perbaikan_kontainer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_cat', function (Blueprint $table) {
            // Reverse the changes
            $table->renameColumn('tanggal_cat', 'tanggal_tagihan');

            // Add back jumlah column
            $table->decimal('jumlah', 15, 2)->after('tanggal_tagihan');

            // Add back perbaikan_kontainer_id column
            $table->foreignId('perbaikan_kontainer_id')->nullable()->after('keterangan');
            $table->foreign('perbaikan_kontainer_id')->references('id')->on('perbaikan_kontainers')->onDelete('set null');

            // Drop nomor_tagihan_cat column
            $table->dropColumn('nomor_tagihan_cat');
        });
    }
};
