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
        Schema::create('tanda_terimas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_jalan_id')->constrained('surat_jalans')->onDelete('cascade');

            // Data dari surat jalan
            $table->string('no_surat_jalan');
            $table->date('tanggal_surat_jalan')->nullable();
            $table->string('supir')->nullable();
            $table->string('kegiatan')->nullable();
            $table->string('size')->nullable();
            $table->integer('jumlah_kontainer')->default(1);
            $table->text('no_kontainer')->nullable();
            $table->string('no_seal')->nullable();
            $table->string('tujuan_pengiriman')->nullable();
            $table->string('pengirim')->nullable();
            $table->string('gambar_checkpoint')->nullable();

            // Kolom tambahan untuk tanda terima
            $table->string('estimasi_nama_kapal')->nullable();
            $table->date('tanggal_ambil_kontainer')->nullable();
            $table->date('tanggal_terima_pelabuhan')->nullable();
            $table->date('tanggal_garasi')->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('satuan')->nullable();
            $table->decimal('berat_kotor', 10, 2)->nullable();
            $table->string('dimensi')->nullable();

            // Metadata
            $table->text('catatan')->nullable();
            $table->string('status')->default('draft'); // draft, submitted, completed
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('no_surat_jalan');
            $table->index('status');
            $table->index('tanggal_surat_jalan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_terimas');
    }
};
