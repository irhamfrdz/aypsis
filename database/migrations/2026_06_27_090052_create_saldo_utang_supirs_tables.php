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
        Schema::create('saldo_utang_supirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->unique()->constrained('karyawans')->onDelete('cascade');
            $table->decimal('saldo', 15, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('riwayat_utang_supirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('tipe', ['penambahan', 'pengurangan']);
            $table->decimal('nominal', 15, 2);
            $table->string('referensi');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_utang_supirs');
        Schema::dropIfExists('saldo_utang_supirs');
    }
};
