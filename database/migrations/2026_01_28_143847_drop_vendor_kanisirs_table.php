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
        Schema::dropIfExists('vendor_kanisirs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('vendor_kanisirs', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->nullable()->unique();
            $table->string('nama');
            $table->string('ukuran')->nullable();
            $table->decimal('harga', 15, 2)->default(0);
            $table->string('tipe')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('catatan')->nullable();
            $table->string('status')->default('aktif');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }
};
