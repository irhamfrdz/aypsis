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
        Schema::table('pembayaran_pranota_perbaikan_kontainers', function (Blueprint $table) {
            $table->foreign('pranota_perbaikan_kontainer_id', 'fk_pembayaran_pranota')->references('id')->on('pranota_perbaikan_kontainers')->onDelete('cascade');
            $table->foreign('created_by', 'fk_pembayaran_created_by')->references('id')->on('users');
            $table->foreign('updated_by', 'fk_pembayaran_updated_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_pranota_perbaikan_kontainers', function (Blueprint $table) {
            $table->dropForeign('fk_pembayaran_pranota');
            $table->dropForeign('fk_pembayaran_created_by');
            $table->dropForeign('fk_pembayaran_updated_by');
        });
    }
};
