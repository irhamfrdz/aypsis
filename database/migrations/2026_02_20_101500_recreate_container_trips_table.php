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
        // Drop old table if exists to recreate with new schema
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('container_trips');
        Schema::enableForeignKeyConstraints();
        
        Schema::create('container_trips', function (Blueprint $table) {
            $table->id();
            // Primary Vendor relationship
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->string('no_kontainer');
            $table->string('ukuran'); // 20 atau 40
            $table->date('tgl_ambil');
            $table->date('tgl_kembali')->nullable();
            $table->decimal('harga_sewa', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('container_trips');
    }
};
