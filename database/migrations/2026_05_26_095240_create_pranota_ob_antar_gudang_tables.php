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
        Schema::create('pranota_ob_antar_gudangs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota')->unique();
            $table->date('tanggal_pranota');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('pranota_ob_antar_gudang_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_ob_antar_gudang_id');
            $table->unsignedBigInteger('tagihan_ob_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('pranota_ob_antar_gudang_id', 'fk_poagi_pranota_id')
                ->references('id')
                ->on('pranota_ob_antar_gudangs')
                ->onDelete('cascade');

            $table->foreign('tagihan_ob_id', 'fk_poagi_tagihan_id')
                ->references('id')
                ->on('tagihan_ob')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_ob_antar_gudang_items');
        Schema::dropIfExists('pranota_ob_antar_gudangs');
    }
};
