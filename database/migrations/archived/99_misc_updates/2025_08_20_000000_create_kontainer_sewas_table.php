<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kontainer_sewas', function (Blueprint $table) {
            $table->id();
            $table->string('vendor');
            $table->string('tarif');
            $table->string('ukuran_kontainer');
            $table->decimal('harga', 15, 2);
            $table->date('tanggal_harga_awal');
            $table->date('tanggal_harga_akhir')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kontainer_sewas');
    }
};
