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
        Schema::create('pranota_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota')->unique();
            $table->date('tanggal_pranota');
            $table->string('nomor_accurate')->nullable();
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->json('items'); // Snapshots of stock items
            $table->string('status')->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_stocks');
    }
};
