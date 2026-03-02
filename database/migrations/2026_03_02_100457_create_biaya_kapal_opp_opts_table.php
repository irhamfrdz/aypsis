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
        Schema::create('biaya_kapal_opp_opts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_kapal_id')->constrained('biaya_kapals')->onDelete('cascade');
            $table->foreignId('pricelist_opp_opt_id')->nullable()->constrained('pricelist_opp_opts')->onDelete('set null');
            $table->string('kapal')->nullable();
            $table->string('voyage')->nullable();
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->decimal('tarif', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_nominal', 15, 2)->default(0);
            $table->decimal('dp', 15, 2)->default(0);
            $table->decimal('sisa_pembayaran', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_opp_opts');
    }
};
