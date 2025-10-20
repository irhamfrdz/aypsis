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
        Schema::create('gate_in_petikemas_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gate_in_id')->constrained('gate_ins')->onDelete('cascade');
            $table->string('no_petikemas')->comment('Nomor kontainer/petikemas');
            $table->string('s_t_s')->comment('Size/Type/Status - ex: 20/DRY/F');
            $table->date('estimasi')->comment('Tanggal estimasi');
            $table->decimal('estimasi_biaya', 15, 2)->default(0)->comment('Total estimasi biaya untuk petikemas ini');
            $table->timestamps();

            // Indexes
            $table->index(['gate_in_id', 'no_petikemas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_in_petikemas_details');
    }
};
