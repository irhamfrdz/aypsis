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
        Schema::create('invoice_aktivitas_lain_listrik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_aktivitas_lain_id')->constrained('invoice_aktivitas_lain')->onDelete('cascade');
            $table->decimal('lwbp_baru', 15, 2)->nullable();
            $table->decimal('lwbp_lama', 15, 2)->nullable();
            $table->decimal('lwbp', 15, 2)->nullable();
            $table->decimal('wbp', 15, 2)->nullable();
            $table->decimal('lwbp_tarif', 15, 2)->nullable();
            $table->decimal('wbp_tarif', 15, 2)->nullable();
            $table->decimal('tarif_1', 15, 2)->nullable();
            $table->decimal('tarif_2', 15, 2)->nullable();
            $table->decimal('biaya_beban', 15, 2)->nullable();
            $table->decimal('ppju', 15, 2)->nullable();
            $table->decimal('dpp', 15, 2)->nullable();
            $table->decimal('pph', 15, 2)->nullable();
            $table->decimal('grand_total', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_aktivitas_lain_listrik');
    }
};
