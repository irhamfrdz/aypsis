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
        Schema::create('perbaikan_kontainers', function (Blueprint $table) {
            $table->id();
            $table->string('no_perbaikan')->unique();
            $table->string('no_kontainer')->index();
            $table->string('ukuran')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->foreignId('vendor_bengkel_id')->constrained('vendor_bengkel');
            $table->text('keterangan_kerusakan');
            $table->text('keterangan_perbaikan')->nullable();
            $table->decimal('estimasi_biaya', 15, 2)->default(0);
            $table->decimal('biaya_riil', 15, 2)->default(0);
            $table->string('status')->default('pending'); // pending, proses, selesai, batal
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perbaikan_kontainers');
    }
};
