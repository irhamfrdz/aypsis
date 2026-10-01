<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan_lolo_batams', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tagihan')->unique();
            $table->date('tanggal_tagihan');
            $table->string('vendor')->nullable();
            $table->string('kapal')->nullable();
            $table->string('voyage')->nullable();
            $table->enum('status_pembayaran', ['Belum Lunas', 'Lunas'])->default('Belum Lunas');
            $table->date('tanggal_bayar')->nullable();
            $table->decimal('total_tagihan', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->string('status_approval', 50)->default('Pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tagihan_lolo_batam_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_lolo_batam_id')->constrained('tagihan_lolo_batams')->onDelete('cascade');
            $table->string('sumber_data', 50)->default('manual'); // 'bongkaran', 'langsir', 'manual'
            $table->unsignedBigInteger('surat_jalan_bongkaran_id')->nullable();
            $table->unsignedBigInteger('langsir_batam_id')->nullable();
            $table->unsignedBigInteger('master_pricelist_lolo_batam_id')->nullable();
            $table->string('nomor_surat_jalan')->nullable();
            $table->string('nomor_kontainer');
            $table->string('size', 10)->nullable();
            $table->string('tipe_kontainer', 50)->nullable(); // FULL, EMPTY
            $table->string('kegiatan')->nullable();
            $table->decimal('tarif', 15, 2)->default(0);
            $table->integer('jumlah')->default(1);
            $table->decimal('total', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('surat_jalan_bongkaran_id');
            $table->index('langsir_batam_id');
            $table->index('nomor_kontainer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_lolo_batam_items');
        Schema::dropIfExists('tagihan_lolo_batams');
    }
};
