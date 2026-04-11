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
        Schema::create('pranota_ongkos_truk_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_ongkos_truk_id');
            $table->unsignedBigInteger('surat_jalan_id')->nullable();
            $table->unsignedBigInteger('surat_jalan_bongkaran_id')->nullable();
            $table->string('no_surat_jalan')->nullable();
            $table->date('tanggal')->nullable();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->string('type')->nullable(); // regular, regular_adj, bongkaran, bongkaran_adj
            $table->timestamps();
            
            $table->foreign('pranota_ongkos_truk_id', 'fk_pot_items_parent')->references('id')->on('pranota_ongkos_truks')->onDelete('cascade');
            $table->foreign('surat_jalan_id')->references('id')->on('surat_jalans')->onDelete('set null');
            $table->foreign('surat_jalan_bongkaran_id', 'fk_pot_items_sjb')->references('id')->on('surat_jalan_bongkarans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_ongkos_truk_items');
    }
};
