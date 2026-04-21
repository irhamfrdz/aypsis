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
        Schema::create('tanda_terima_bongkaran_batams', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tanda_terima')->unique();
            $table->date('tanggal_tanda_terima');
            $table->unsignedBigInteger('surat_jalan_bongkaran_id')->nullable();
            $table->unsignedBigInteger('gudang_id')->nullable();
            $table->string('no_kontainer')->nullable();
            $table->string('no_seal')->nullable();
            $table->string('kegiatan')->nullable();
            $table->string('status')->default('active');
            $table->text('keterangan')->nullable();
            $table->boolean('lembur')->default(false);
            $table->boolean('nginap')->default(false);
            $table->boolean('tidak_lembur_nginap')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('surat_jalan_bongkaran_id', 'ttbb_sj_bongkaran_foreign')->references('id')->on('surat_jalan_bongkarans')->onDelete('cascade');
            $table->foreign('gudang_id', 'ttbb_gudang_foreign')->references('id')->on('gudangs')->onDelete('set null');
            $table->foreign('created_by', 'ttbb_created_by_foreign')->references('id')->on('users');
            $table->foreign('updated_by', 'ttbb_updated_by_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_terima_bongkaran_batams');
    }
};
