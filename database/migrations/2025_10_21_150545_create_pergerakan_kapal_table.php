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
        Schema::create('pergerakan_kapal', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kapal');
            $table->string('kapten')->nullable();
            $table->string('voyage')->nullable();
            $table->boolean('transit')->default(false);
            $table->string('pelabuhan_asal');
            $table->string('pelabuhan_tujuan');
            $table->string('pelabuhan_transit')->nullable();
            $table->string('voyage_transit')->nullable();
            $table->datetime('tanggal_sandar')->nullable();
            $table->datetime('tanggal_labuh')->nullable();
            $table->datetime('tanggal_berangkat')->nullable();
            $table->enum('status', ['scheduled', 'sailing', 'arrived', 'departed', 'delayed', 'cancelled'])->default('scheduled');
            $table->text('keterangan')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index('nama_kapal');
            $table->index('voyage');
            $table->index('status');
            $table->index('tanggal_sandar');
            $table->index('tanggal_berangkat');
            $table->index(['pelabuhan_asal', 'pelabuhan_tujuan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pergerakan_kapal');
    }
};
