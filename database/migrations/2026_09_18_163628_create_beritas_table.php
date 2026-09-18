<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beritas', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('konten')->nullable();
            $table->enum('tipe', ['berita', 'pamflet'])->default('berita');
            $table->string('gambar')->nullable(); // path file gambar/pamflet
            $table->boolean('is_active')->default(true);
            $table->boolean('pinned')->default(false); // berita penting
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beritas');
    }
};

