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
        Schema::create('biaya_kapals', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_kapal');
            $table->enum('jenis_biaya', ['bahan_bakar', 'pelabuhan', 'perbaikan', 'awak_kapal', 'asuransi', 'lainnya']);
            $table->decimal('nominal', 15, 2);
            $table->text('keterangan')->nullable();
            $table->string('bukti')->nullable(); // File path for bukti (PDF/image)
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('tanggal');
            $table->index('nama_kapal');
            $table->index('jenis_biaya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapals');
    }
};
