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
        Schema::create('tanda_terima_lcl_kontainer_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanda_terima_lcl_id')->constrained('tanda_terimas_lcl')->onDelete('cascade');
            
            // Container Information
            $table->string('nomor_kontainer')->nullable();
            $table->string('size_kontainer')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('nomor_seal')->nullable();
            $table->date('tanggal_seal')->nullable();
            
            // Tracking
            $table->timestamp('assigned_at')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Indexes for performance (with custom names to avoid MySQL length limit)
            $table->index('nomor_kontainer', 'idx_lcl_pivot_kontainer');
            $table->index(['nomor_kontainer', 'tanda_terima_lcl_id'], 'idx_lcl_pivot_kontainer_tt');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_terima_lcl_kontainer_pivot');
    }
};
