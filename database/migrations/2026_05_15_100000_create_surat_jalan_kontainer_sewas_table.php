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
        Schema::create('surat_jalan_kontainer_sewas', function (Blueprint $table) {
            $table->id();

            // Nomor surat jalan
            $table->string('nomor_surat_jalan')->unique();

            // Tipe: pengambilan atau pengembalian
            $table->enum('tipe', ['pengambilan', 'pengembalian']);

            // Tanggal surat jalan
            $table->date('tanggal');

            // Informasi vendor
            $table->string('vendor')->nullable();

            // Informasi supir/driver
            $table->string('supir')->nullable();
            $table->string('no_plat')->nullable();

            // Lokasi
            $table->string('lokasi_pengambilan')->nullable();
            $table->string('lokasi_pengembalian')->nullable();

            // Keterangan tambahan
            $table->text('keterangan')->nullable();

            // Status surat jalan
            $table->enum('status', ['draft', 'aktif', 'selesai', 'batal'])->default('draft');

            // User tracking
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('tipe');
            $table->index('tanggal');
            $table->index('vendor');
            $table->index('status');
        });

        // Pivot table: surat_jalan_kontainer_sewa_items (detail kontainer per SJ)
        Schema::create('surat_jalan_kontainer_sewa_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('surat_jalan_kontainer_sewa_id');
            $table->foreign('surat_jalan_kontainer_sewa_id', 'sj_kontainer_sewa_id_fk')
                ->references('id')
                ->on('surat_jalan_kontainer_sewas')
                ->onDelete('cascade');

            // Kontainer info
            $table->string('nomor_kontainer');
            $table->string('ukuran')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('vendor')->nullable();

            // Kondisi kontainer saat pengambilan/pengembalian
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->text('catatan_kondisi')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('surat_jalan_kontainer_sewa_id', 'sj_ks_item_sj_id_idx');
            $table->index('nomor_kontainer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan_kontainer_sewa_items');
        Schema::dropIfExists('surat_jalan_kontainer_sewas');
    }
};
