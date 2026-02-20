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
        Schema::create('vendor_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendor_kontainer_sewas');
            $table->string('no_invoice')->unique();
            $table->date('tgl_invoice');
            $table->decimal('total_dpp', 15, 2);
            $table->decimal('total_ppn', 15, 2);
            $table->decimal('total_pph23', 15, 2);
            $table->decimal('total_materai', 15, 2)->default(0);
            $table->decimal('total_netto', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_invoices');
    }
};
