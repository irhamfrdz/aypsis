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
        Schema::create('uang_lemburs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('tipe_hari'); // 'Hari Kerja' atau 'Hari Libur'
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->decimal('total_jam', 5, 2)->default(0);
            $table->decimal('nominal_uang', 15, 2)->default(0);
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uang_lemburs');
    }
};
