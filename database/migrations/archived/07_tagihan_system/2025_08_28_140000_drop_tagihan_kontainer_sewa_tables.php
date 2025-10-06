<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // create a safe migration to drop the tagihan tables and any dependent tables/constraints
        // 1) drop dependent pembayaran_pranota_tagihan_kontainer_tagihan table if present (it references tagihan_kontainer_sewa)
        if (Schema::hasTable('pembayaran_pranota_tagihan_kontainer_tagihan')) {
            Schema::dropIfExists('pembayaran_pranota_tagihan_kontainer_tagihan');
        }

        // 2) drop pivot table if present
        if (Schema::hasTable('tagihan_kontainer_sewa_kontainers')) {
            Schema::dropIfExists('tagihan_kontainer_sewa_kontainers');
        }

        // 3) drop main tagihan table
        if (Schema::hasTable('tagihan_kontainer_sewa')) {
            Schema::dropIfExists('tagihan_kontainer_sewa');
        }
    }

    public function down()
    {
        // recreate minimal structure to restore tables if needed for rollbacks
        if (!Schema::hasTable('tagihan_kontainer_sewa')) {
            Schema::create('tagihan_kontainer_sewa', function (Blueprint $table) {
                $table->id();
                $table->string('vendor')->nullable();
                $table->string('tarif')->nullable();
                $table->date('tanggal_harga_awal')->nullable();
                $table->date('tanggal_harga_akhir')->nullable();
                $table->text('keterangan')->nullable();
                $table->string('nomor_kontainer')->nullable();
                $table->string('group_code')->nullable();
                $table->string('status_pembayaran')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('tagihan_kontainer_sewa_kontainers')) {
            Schema::create('tagihan_kontainer_sewa_kontainers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tagihan_id')->nullable();
                $table->unsignedBigInteger('kontainer_id')->nullable();
                $table->decimal('harga', 15, 2)->nullable();
                $table->decimal('dpp', 15, 2)->nullable();
                $table->decimal('ppn', 15, 2)->nullable();
                $table->decimal('pph', 15, 2)->nullable();
                $table->decimal('grand_total', 15, 2)->nullable();
                $table->timestamps();
            });
        }
    }
};
