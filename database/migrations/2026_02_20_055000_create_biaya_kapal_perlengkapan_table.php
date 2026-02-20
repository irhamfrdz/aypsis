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
        Schema::create('biaya_kapal_perlengkapan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('biaya_kapal_id');
            $table->string('nama_kapal')->nullable();
            $table->string('no_voyage')->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('jumlah_biaya', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('biaya_kapal_id')
                  ->references('id')
                  ->on('biaya_kapals')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_perlengkapan');
    }
};
