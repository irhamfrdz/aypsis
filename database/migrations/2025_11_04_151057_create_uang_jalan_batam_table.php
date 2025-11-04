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
        Schema::create('uang_jalan_batam', function (Blueprint $table) {
            $table->id();
            $table->string('wilayah');
            $table->string('rute');
            $table->string('expedisi');
            $table->string('ring');
            $table->string('ft'); // 20ft, 40ft, dll
            $table->string('f_e'); // Full/Empty
            $table->decimal('tarif', 15, 2);
            $table->enum('status', ['aqua', 'chasis PB'])->nullable(); // status bisa aqua, chasis PB, atau null
            $table->date('tanggal_berlaku');
            $table->timestamps();
            
            // Index untuk pencarian yang sering dilakukan
            $table->index(['wilayah', 'rute']);
            $table->index(['expedisi', 'ring']);
            $table->index('tanggal_berlaku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uang_jalan_batam');
    }
};
