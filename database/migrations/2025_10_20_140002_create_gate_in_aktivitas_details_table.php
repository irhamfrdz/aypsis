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
        Schema::create('gate_in_aktivitas_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gate_in_id')->constrained('gate_ins')->onDelete('cascade');
            $table->string('aktivitas')->comment('Nama aktivitas seperti ADMIN NOTA, HAULAGE, LOLO, dll');
            $table->string('s_t_s')->comment('Size/Type/Status - ex: 20/DRY/F, 0/-/- untuk admin');
            $table->integer('box')->comment('Jumlah box/kontainer');
            $table->integer('itm')->comment('Jumlah item');
            $table->decimal('tarif', 15, 2)->comment('Tarif per unit');
            $table->decimal('total', 15, 2)->comment('Total biaya (box * itm * tarif)');
            $table->timestamps();

            // Indexes
            $table->index(['gate_in_id', 'aktivitas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_in_aktivitas_details');
    }
};
