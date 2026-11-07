<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biaya_kapal_pelindos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_kapal_id')->unique()->constrained('biaya_kapals')->cascadeOnDelete();
            $table->string('nama_kapal');
            $table->string('no_voyage');
            $table->timestamps();
        });

        if (! DB::table('klasifikasi_biayas')->where('nama', 'Tagihan Pelindo')->exists()) {
            DB::table('klasifikasi_biayas')->insert([
                'kode' => 'KB055',
                'nama' => 'Tagihan Pelindo',
                'deskripsi' => 'Tagihan Pelindo berdasarkan kapal dan voyage manifest',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_pelindos');
        DB::table('klasifikasi_biayas')->where('kode', 'KB055')->where('nama', 'Tagihan Pelindo')->delete();
    }
};
