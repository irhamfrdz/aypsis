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
        Schema::create('stock_amprahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_nama_barang_amprahan_id')->constrained('master_nama_barang_amprahans')->onDelete('cascade');
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->string('satuan')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_amprahans');
    }
};
