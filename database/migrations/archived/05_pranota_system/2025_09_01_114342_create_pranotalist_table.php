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
        Schema::create('pranotalist', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice')->unique();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'sent', 'paid', 'cancelled'])->default('draft');
            $table->json('tagihan_ids'); // Array of tagihan IDs that are included in this pranota
            $table->integer('jumlah_tagihan')->default(0); // Count of tagihan items
            $table->date('tanggal_pranota');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranotalist');
    }
};
