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
        // Drop tabel pivot penerima LCL jika ada
        Schema::dropIfExists('tanda_terima_lcl_penerima');
        
        // Drop tabel pivot pengirim LCL jika ada
        Schema::dropIfExists('tanda_terima_lcl_pengirim');
        
        // Drop tabel pivot kontainer LCL jika ada
        Schema::dropIfExists('kontainer_tanda_terima_lcl');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate tabel pivot penerima
        Schema::create('tanda_terima_lcl_penerima', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanda_terima_lcl_id')->constrained('tanda_terimas_lcl')->onDelete('cascade');
            $table->string('nama_penerima');
            $table->string('pic_penerima')->nullable();
            $table->string('telepon_penerima')->nullable();
            $table->text('alamat_penerima');
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });
        
        // Recreate tabel pivot pengirim
        Schema::create('tanda_terima_lcl_pengirim', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanda_terima_lcl_id')->constrained('tanda_terimas_lcl')->onDelete('cascade');
            $table->string('nama_pengirim');
            $table->string('pic_pengirim')->nullable();
            $table->string('telepon_pengirim')->nullable();
            $table->text('alamat_pengirim');
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });
        
        // Recreate tabel pivot kontainer
        Schema::create('kontainer_tanda_terima_lcl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanda_terima_lcl_id')->constrained('tanda_terimas_lcl')->onDelete('cascade');
            $table->string('nomor_kontainer');
            $table->string('size_kontainer')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->timestamps();
        });
    }
};
