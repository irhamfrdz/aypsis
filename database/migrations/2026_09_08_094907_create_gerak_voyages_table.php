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
        Schema::create('gerak_voyages', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kapal');
            $table->string('no_voyage');
            $table->date('tanggal_mulai_berlayar')->nullable();
            $table->date('tanggal_berlabuh')->nullable();
            $table->date('tanggal_sandar')->nullable();
            $table->date('tanggal_mulai_bongkar')->nullable();
            $table->date('tanggal_selesai_bongkar')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gerak_voyages');
    }
};
