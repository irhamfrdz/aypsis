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
        Schema::create('master_pricelist_ob', function (Blueprint $table) {
            $table->id();
            $table->string('size_kontainer'); // 20ft, 40ft
            $table->enum('status_kontainer', ['full', 'empty']); // status kontainer
            $table->decimal('biaya', 15, 2); // biaya OB
            $table->text('keterangan')->nullable(); // keterangan tambahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_pricelist_ob');
    }
};
