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
        Schema::create('master_pricelist_uang_jalan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique()->comment('Kode unik untuk setiap rute');
            $table->string('cabang', 50)->comment('Kode cabang (JKT, SBY, dll)');
            $table->string('wilayah', 100)->comment('Nama wilayah tujuan');
            $table->string('dari', 100)->comment('Lokasi asal/garasi');
            $table->string('ke', 100)->comment('Lokasi tujuan');
            
            // Tarif uang jalan berdasarkan ukuran kontainer
            $table->decimal('uang_jalan_20ft', 15, 2)->default(0)->comment('Uang jalan kontainer 20 feet');
            $table->decimal('uang_jalan_40ft', 15, 2)->default(0)->comment('Uang jalan kontainer 40 feet');
            
            // Informasi tambahan
            $table->text('keterangan')->nullable()->comment('Keterangan khusus untuk rute ini');
            $table->integer('liter')->default(0)->comment('Estimasi liter BBM yang dibutuhkan');
            $table->decimal('jarak_km', 8, 2)->default(0)->comment('Jarak dari Penjaringan dalam KM');
            
            // Tarif mel (handling)
            $table->decimal('mel_20ft', 15, 2)->default(0)->comment('Tarif mel/handling 20 feet');
            $table->decimal('mel_40ft', 15, 2)->default(0)->comment('Tarif mel/handling 40 feet');
            
            // Tarif ongkos dan antar lokasi
            $table->decimal('ongkos_truk_20ft', 15, 2)->default(0)->comment('Ongkos truk 20 feet');
            $table->decimal('antar_lokasi_20ft', 15, 2)->default(0)->comment('Biaya antar lokasi 20 feet');
            $table->decimal('antar_lokasi_40ft', 15, 2)->default(0)->comment('Biaya antar lokasi 40 feet');
            
            // Status dan tracking
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->date('berlaku_dari')->default(now());
            $table->date('berlaku_sampai')->nullable();
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['cabang', 'status']);
            $table->index(['wilayah', 'status']);
            $table->index(['dari', 'ke']);
            $table->index(['berlaku_dari', 'berlaku_sampai']);
            
            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_pricelist_uang_jalan');
    }
};