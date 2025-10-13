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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Basic order information
            $table->string('nomor_order')->unique();
            $table->date('tanggal_order');
            $table->string('tujuan_kirim');
            $table->string('no_tiket_do')->nullable();
            $table->string('tujuan_ambil');

            // Container information
            $table->string('size_kontainer');
            $table->integer('unit_kontainer');
            $table->enum('tipe_kontainer', ['fcl', 'lcl', 'cargo', 'fcl_plus']);
            $table->date('tanggal_pickup')->nullable();

            // Document types (boolean flags)
            $table->boolean('exclude_ftz03')->default(false);
            $table->boolean('include_ftz03')->default(false);
            $table->boolean('exclude_sppb')->default(false);
            $table->boolean('include_sppb')->default(false);
            $table->boolean('exclude_buruh_bongkar')->default(false);
            $table->boolean('include_buruh_bongkar')->default(false);

            // Foreign keys to master data
            $table->foreignId('term_id')->nullable()->constrained('terms')->onDelete('set null');
            $table->foreignId('pengirim_id')->nullable()->constrained('pengirims')->onDelete('set null');
            $table->foreignId('jenis_barang_id')->nullable()->constrained('jenis_barangs')->onDelete('set null');

            // Status and audit fields
            $table->enum('status', ['draft', 'confirmed', 'processing', 'completed', 'cancelled'])->default('draft');
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
