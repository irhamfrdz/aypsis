<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('history_kontainers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontainer')->index();
            $table->string('tipe_kontainer')->nullable(); // 'kontainer' or 'stock'
            $table->string('jenis_kegiatan'); // 'Masuk' or 'Keluar'
            $table->date('tanggal_kegiatan');
            $table->foreignId('gudang_id')->nullable()->constrained('gudangs')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_kontainers');
    }
};
