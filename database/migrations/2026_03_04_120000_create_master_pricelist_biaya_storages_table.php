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
        Schema::create('master_pricelist_biaya_storages', function (Blueprint $table) {
            $table->id();
            $table->string('vendor')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('size_kontainer')->comment('20ft, 40ft, etc');
            $table->decimal('biaya_per_hari', 15, 2)->default(0);
            $table->integer('free_time')->default(0)->comment('Berapa hari gratis');
            $table->string('status')->default('aktif')->comment('aktif, non-aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_pricelist_biaya_storages');
    }
};
