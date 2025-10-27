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
        Schema::create('tanda_terima_lcl_items', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to main LCL record
            $table->foreignId('tanda_terima_lcl_id')->constrained('tanda_terima_lcl')->onDelete('cascade');
            
            // Item sequence number
            $table->integer('item_number')->default(1);
            
            // Dimensions in centimeters
            $table->decimal('panjang', 10, 2)->nullable()->comment('Length in cm');
            $table->decimal('lebar', 10, 2)->nullable()->comment('Width in cm');  
            $table->decimal('tinggi', 10, 2)->nullable()->comment('Height in cm');
            
            // Calculated volume in cubic meters
            $table->decimal('meter_kubik', 15, 6)->nullable()->comment('Volume in m³');
            
            // Weight in tons
            $table->decimal('tonase', 10, 2)->nullable()->comment('Weight in tons');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['tanda_terima_lcl_id']);
            $table->index(['item_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_terima_lcl_items');
    }
};
