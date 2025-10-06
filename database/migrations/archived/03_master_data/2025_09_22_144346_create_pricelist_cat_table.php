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
        Schema::create('pricelist_cat', function (Blueprint $table) {
            $table->id();
            $table->string('vendor')->nullable();
            $table->string('jenis_cat')->nullable(); // cat_sebagian, cat_full
            $table->decimal('tarif_per_meter', 15, 2)->nullable();
            $table->string('ukuran_kontainer')->nullable(); // 20ft, 40ft, dll
            $table->date('tanggal_harga_awal')->nullable();
            $table->date('tanggal_harga_akhir')->nullable();
            $table->decimal('harga', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['vendor']);
            $table->index(['jenis_cat']);
            $table->index(['ukuran_kontainer']);
            $table->index(['tanggal_harga_awal']);
            $table->index(['tanggal_harga_akhir']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricelist_cat');
    }
};
