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
        Schema::create('perincians', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_bl')->nullable();
            $table->integer('nomor_urut')->nullable();
            $table->string('nomor_manifest')->nullable();
            $table->string('nomor_tanda_terima')->nullable();
            $table->unsignedBigInteger('prospek_id')->nullable();
            $table->unsignedBigInteger('shipper_id')->nullable();
            $table->string('nomor_kontainer')->nullable();
            $table->string('no_seal')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('size_kontainer')->nullable();
            $table->string('no_voyage')->nullable();
            $table->string('pelabuhan_asal')->nullable();
            $table->string('pelabuhan_tujuan')->nullable();
            $table->string('pelabuhan_muat')->nullable();
            $table->string('pelabuhan_bongkar')->nullable();
            $table->string('nama_kapal')->nullable();
            $table->date('tanggal_berangkat')->nullable();
            $table->text('nama_barang')->nullable();
            $table->string('asal_kontainer')->nullable();
            $table->string('ke')->nullable();
            $table->string('pengirim')->nullable();
            $table->text('alamat_pengirim')->nullable();
            $table->string('penerima')->nullable();
            $table->text('alamat_penerima')->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->string('contact_person')->nullable();
            $table->decimal('tonnage', 10, 3)->nullable();
            $table->decimal('tonnage_perincian', 10, 3)->nullable();
            $table->decimal('volume', 10, 3)->nullable();
            $table->decimal('volume_perincian', 10, 3)->nullable();
            $table->string('satuan')->nullable();
            $table->string('term')->nullable();
            $table->integer('kuantitas')->nullable();
            $table->string('hs_code')->nullable();
            $table->date('penerimaan')->nullable();
            $table->string('notify_party')->nullable();
            $table->text('alamat_notify_party')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('nama_kapal');
            $table->index('no_voyage');
            $table->index('nomor_kontainer');
            $table->index('prospek_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perincians');
    }
};
