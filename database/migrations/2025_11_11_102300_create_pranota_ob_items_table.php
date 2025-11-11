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
        Schema::create('pranota_ob_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pranota_ob_id')->constrained('pranota_ob')->onDelete('cascade');
            $table->foreignId('tagihan_ob_id')->constrained('tagihan_ob')->onDelete('cascade');
            $table->decimal('amount', 15, 2); // Biaya saat ditambahkan ke pranota
            $table->text('notes')->nullable(); // Catatan khusus untuk item ini
            $table->timestamps();
            
            // Composite unique constraint - satu tagihan ob hanya bisa ada di satu pranota
            $table->unique(['tagihan_ob_id']);
            
            // Indexes for better performance
            $table->index(['pranota_ob_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_ob_items');
    }
};
