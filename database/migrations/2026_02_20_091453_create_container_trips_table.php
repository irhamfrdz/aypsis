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
        Schema::create('container_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendor_kontainer_sewas');
            $table->string('no_container');
            $table->enum('size', ['20', '40']);
            $table->date('tgl_ambil');
            $table->date('tgl_kembali')->nullable();
            $table->decimal('harga_sewa_per_bulan', 15, 2);
            $table->string('status')->default('ACTIVE'); // ACTIVE, CLOSED
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('container_trips');
        Schema::enableForeignKeyConstraints();
    }
};
