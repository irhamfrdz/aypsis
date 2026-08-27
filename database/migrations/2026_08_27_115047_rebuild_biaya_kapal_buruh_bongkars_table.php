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
        // First drop the old details table
        Schema::dropIfExists('biaya_kapal_buruh_bongkar_details');
        
        // Drop the old main table
        Schema::dropIfExists('biaya_kapal_buruh_bongkars');

        // Recreate it with Batam style fields
        Schema::create('biaya_kapal_buruh_bongkars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('biaya_kapal_id');
            $table->foreign('biaya_kapal_id', 'fk_bkbb_new_biaya_kapal_id')->references('id')->on('biaya_kapals')->onDelete('cascade');
            
            // New Batam-style fields
            $table->string('kapal')->nullable();
            $table->string('voyage')->nullable();
            $table->json('kontainer_ids')->nullable();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->string('notes_adjustment')->nullable();
            $table->decimal('total_nominal', 15, 2)->default(0);
            $table->string('nomor_bukti')->nullable();
            $table->string('penerima')->nullable();
            $table->string('nama_vendor')->nullable();
            
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->foreign('bank_id', 'fk_bkbb_new_bank_id')->references('id')->on('banks')->onDelete('set null');
            
            $table->string('nomor_rekening')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_buruh_bongkars');
    }
};
