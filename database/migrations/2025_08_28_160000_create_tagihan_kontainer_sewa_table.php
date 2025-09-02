<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagihanKontainerSewaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->id();
            $table->string('vendor')->nullable();
            $table->string('nomor_kontainer')->nullable();
            $table->string('group_code')->nullable();
            $table->string('group')->nullable();
            $table->date('tanggal_harga_awal')->nullable();
            $table->date('tanggal_harga_akhir')->nullable();
            $table->string('periode')->nullable();
            $table->decimal('massa', 12, 2)->nullable();
            $table->decimal('dpp', 15, 2)->nullable();
            $table->decimal('dpp_nilai_lain', 15, 2)->nullable();
            $table->decimal('ppn', 15, 2)->nullable();
            $table->decimal('pph', 15, 2)->nullable();
            $table->decimal('grand_total', 18, 2)->nullable();

            // Additional sensible fields referenced elsewhere or useful for CRUD
            $table->string('ukuran_kontainer')->nullable();
            $table->decimal('tarif', 15, 2)->nullable();
            $table->decimal('harga', 15, 2)->nullable();
            $table->string('status_pembayaran')->nullable();
            $table->string('nomor_pranota')->nullable();
            $table->boolean('is_pranota')->default(false);
            $table->timestamp('tanggal_checkpoint_supir')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tagihan_kontainer_sewa');
    }
}
