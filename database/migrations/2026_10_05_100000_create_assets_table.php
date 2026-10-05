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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_asset', 50)->unique();
            $table->string('nama_asset', 255);
            $table->string('kategori', 100)->index();
            $table->string('merk', 100)->nullable();
            $table->string('tipe_model', 100)->nullable();
            $table->string('nomor_seri', 100)->nullable()->index();
            $table->string('lokasi', 150)->nullable()->index();
            $table->date('tanggal_perolehan')->nullable();
            $table->decimal('nilai_perolehan', 15, 2)->default(0);
            $table->integer('masa_manfaat_bulan')->nullable()->comment('Masa manfaat dalam hitungan bulan');
            $table->decimal('nilai_residu', 15, 2)->default(0);
            $table->decimal('nilai_buku', 15, 2)->default(0);
            $table->string('kondisi', 50)->default('Baik')->index();
            $table->string('status', 50)->default('Tersedia')->index();
            $table->string('penanggung_jawab', 150)->nullable();
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->string('vendor', 150)->nullable();
            $table->string('nomor_faktur', 100)->nullable();
            $table->string('foto', 255)->nullable();
            $table->string('lampiran', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
