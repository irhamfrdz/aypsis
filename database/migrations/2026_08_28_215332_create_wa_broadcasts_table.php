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
        Schema::create('wa_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kapal');
            $table->string('no_voyage');
            $table->string('kategori_masalah');
            $table->text('deskripsi_masalah')->nullable();
            $table->foreignId('wa_template_id')->nullable()->constrained('wa_templates')->nullOnDelete();
            $table->integer('total_shipper')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_broadcasts');
    }
};
