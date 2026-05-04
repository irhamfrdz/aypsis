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
        Schema::create('kwitansi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kwitansi_id')->constrained()->onDelete('cascade');
            $table->string('item_kode')->nullable();
            $table->string('item_description')->nullable();
            $table->integer('qty')->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('no_bl')->nullable();
            $table->string('no_sj')->nullable();
            $table->string('dept')->nullable();
            $table->string('proyek')->nullable();
            $table->string('sn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kwitansi_details');
    }
};
