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
        Schema::dropIfExists('ob_bongkar');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('ob_bongkar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_naik_kapal');
            $table->date('tanggal_bongkar');
            $table->time('jam_bongkar');
            $table->decimal('total_tonase', 10, 3);
            $table->integer('kuantitas');
            $table->text('keterangan')->nullable();
            $table->enum('status_bongkar', ['belum bongkar', 'sudah bongkar'])->default('belum bongkar');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('id_naik_kapal')->references('id')->on('naik_kapal')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};
