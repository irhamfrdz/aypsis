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
        Schema::create('naik_kapal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prospek_id');
            $table->string('nomor_kontainer');
            $table->string('no_seal')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('ukuran_kontainer')->nullable();
            $table->string('nama_kapal')->nullable();
            $table->string('no_voyage')->nullable();
            $table->string('pelabuhan_asal')->nullable();
            $table->string('pelabuhan_tujuan')->nullable();
            $table->date('tanggal_muat')->nullable();
            $table->time('jam_muat')->nullable();
            $table->decimal('total_volume', 12, 3)->nullable()->comment('Total volume in m³');
            $table->decimal('total_tonase', 10, 3)->nullable()->comment('Total weight in tons');
            $table->integer('kuantitas')->nullable()->comment('Quantity of items');
            $table->string('status')->default('menunggu')->comment('menunggu, dimuat, selesai, batal');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('prospek_id')->references('id')->on('prospek')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('prospek_id');
            $table->index('status');
            $table->index('tanggal_muat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('naik_kapal');
    }
};
