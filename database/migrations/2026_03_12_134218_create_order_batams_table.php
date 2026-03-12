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
        Schema::create('order_batams', function (Blueprint $table) {
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

            // Additional fields added in various migrations
            $table->boolean('exclude_ftz03')->default(false);
            $table->boolean('include_ftz03')->default(false);
            $table->boolean('exclude_sppb')->default(false);
            $table->boolean('include_sppb')->default(false);
            $table->boolean('exclude_buruh_bongkar')->default(false);
            $table->boolean('include_buruh_bongkar')->default(false);
            
            // Satuan
            $table->enum('satuan', ['kg', 'ton', 'm3', 'unit', 'pcs', 'dus', 'karung', 'kontainer'])->nullable();
            
            // Penerima 
            $table->string('penerima')->nullable();
            $table->foreignId('penerima_id')->nullable()->constrained('penerimas')->onDelete('set null');
            $table->text('alamat_penerima')->nullable();
            $table->string('kontak_penerima')->nullable();
            $table->foreignId('notify_party_id')->nullable()->constrained('penerimas')->onDelete('set null');
            $table->foreignId('tujuan_ambil_id')->nullable()->constrained('tujuan_kegiatan_utamas')->onDelete('set null');
            
            // Track processing status
            $table->integer('units')->default(1);
            $table->integer('sisa')->default(1);
            $table->string('outstanding_status')->default('pending');
            $table->decimal('completion_percentage', 5, 2)->default(0.00);
            $table->timestamp('completed_at')->nullable();
            $table->json('processing_history')->nullable();

            // Foreign keys to master data
            $table->foreignId('term_id')->nullable()->constrained('terms')->onDelete('set null');
            $table->foreignId('pengirim_id')->nullable()->constrained('pengirims')->onDelete('set null');
            $table->foreignId('jenis_barang_id')->nullable()->constrained('jenis_barangs')->onDelete('set null');

            // Status and audit fields
            $table->enum('status', ['draft', 'confirmed', 'processing', 'completed', 'cancelled'])->default('draft');
            $table->text('catatan')->nullable();

            // Auditable trait fields
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_batams');
    }
};
