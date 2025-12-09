<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pranota_ob_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_ob_id');
            $table->string('item_type')->nullable(); // Eloquent morph class or type alias
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('nomor_kontainer')->nullable();
            $table->string('nama_barang')->nullable();
            $table->string('supir')->nullable();
            $table->string('size')->nullable();
            $table->decimal('biaya', 14, 2)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('pranota_ob_id')->references('id')->on('pranota_obs')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['item_type', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pranota_ob_items');
    }
};
