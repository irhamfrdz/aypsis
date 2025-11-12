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
        Schema::create('pricelist_tarif_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricelist_uang_jalan_batam_id')->constrained('pricelist_uang_jalan_batam')->onDelete('cascade');
            $table->foreignId('kelola_bbm_id')->nullable()->constrained('kelola_bbm')->onDelete('set null');
            $table->decimal('tarif_lama', 15, 2);
            $table->decimal('tarif_baru', 15, 2);
            $table->decimal('persentase_perubahan', 10, 2);
            $table->decimal('persentase_bbm', 10, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricelist_tarif_history');
    }
};
