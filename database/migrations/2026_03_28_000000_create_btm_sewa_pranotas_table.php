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
        Schema::create('btm_sewa_pranotas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique();
            $table->foreignId('vendor_id')->constrained('btm_sewa_vendors');
            $table->string('no_invoice')->nullable();
            $table->string('tgl_invoice')->nullable();
            $table->decimal('total_aypsis', 18, 2)->default(0);
            $table->decimal('total_vendor_bill', 18, 2)->default(0);
            $table->decimal('dpp', 18, 2)->default(0);
            $table->decimal('ppn', 18, 2)->default(0);
            $table->decimal('pph', 18, 2)->default(0);
            $table->decimal('grand_total', 18, 2)->default(0);
            $table->string('status')->default('PENDING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('btm_sewa_pranotas');
    }
};
