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
        Schema::create('langsir_batams', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->date('tanggal');
            $table->string('no_kontainer');
            $table->string('size');
            $table->string('no_seal')->nullable();
            $table->string('dari');
            $table->string('ke');
            $table->string('no_plat')->nullable();
            $table->string('supir')->nullable();
            $table->decimal('biaya', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('input_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('input_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('langsir_batams');
    }
};
