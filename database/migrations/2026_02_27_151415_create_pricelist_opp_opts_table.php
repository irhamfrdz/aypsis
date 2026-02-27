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
        Schema::create('pricelist_opp_opts', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->decimal('tarif', 15, 2)->default(0);
            $table->string('status')->default('Aktif'); // Aktif / Non Aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricelist_opp_opts');
    }
};
