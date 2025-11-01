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
        if (!Schema::hasTable('tanda_terima_dimensi_items')) {
            Schema::create('tanda_terima_dimensi_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tanda_terima_tanpa_surat_jalan_id');
                $table->decimal('panjang', 10, 2)->nullable();
                $table->decimal('lebar', 10, 2)->nullable();
                $table->decimal('tinggi', 10, 2)->nullable();
                $table->decimal('meter_kubik', 12, 6)->nullable();
                $table->decimal('tonase', 10, 2)->nullable();
                $table->integer('urutan')->default(1);
                $table->timestamps();

                $table->foreign('tanda_terima_tanpa_surat_jalan_id', 'fk_dimensi_items_tanda_terima')
                      ->references('id')
                      ->on('tanda_terima_tanpa_surat_jalan')
                      ->onDelete('cascade');

                $table->index(['tanda_terima_tanpa_surat_jalan_id', 'urutan'], 'idx_dimensi_items_sj_urutan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_terima_dimensi_items');
    }
};
