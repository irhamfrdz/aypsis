<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_pricelist_lolo_batams', function (Blueprint $table) {
            $table->id();
            $table->string('vendor')->nullable();
            $table->string('nama_biaya');
            $table->string('kegiatan')->default('LOLO Batam');
            $table->string('size', 10)->default('20');
            $table->string('tipe', 20)->default('FULL');
            $table->decimal('tarif', 15, 2)->default(0);
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert initial seed data for Batam
        $seeds = [
            [
                'vendor' => 'Meratus',
                'nama_biaya' => 'Biaya LOLO Batam 20ft FULL',
                'kegiatan' => 'LOLO Batam',
                'size' => '20',
                'tipe' => 'FULL',
                'tarif' => 450000.00,
                'status' => 'aktif',
                'keterangan' => 'Tarif LOLO kontainer 20ft Full di Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'vendor' => 'Meratus',
                'nama_biaya' => 'Biaya LOLO Batam 20ft EMPTY',
                'kegiatan' => 'LOLO Batam',
                'size' => '20',
                'tipe' => 'EMPTY',
                'tarif' => 350000.00,
                'status' => 'aktif',
                'keterangan' => 'Tarif LOLO kontainer 20ft Empty di Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'vendor' => 'Meratus',
                'nama_biaya' => 'Biaya LOLO Batam 40ft FULL',
                'kegiatan' => 'LOLO Batam',
                'size' => '40',
                'tipe' => 'FULL',
                'tarif' => 750000.00,
                'status' => 'aktif',
                'keterangan' => 'Tarif LOLO kontainer 40ft Full di Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'vendor' => 'Meratus',
                'nama_biaya' => 'Biaya LOLO Batam 40ft EMPTY',
                'kegiatan' => 'LOLO Batam',
                'size' => '40',
                'tipe' => 'EMPTY',
                'tarif' => 600000.00,
                'status' => 'aktif',
                'keterangan' => 'Tarif LOLO kontainer 40ft Empty di Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'vendor' => 'Pelindo Batam',
                'nama_biaya' => 'Biaya LOLO Pelindo Batam 20ft',
                'kegiatan' => 'LOLO Batam',
                'size' => '20',
                'tipe' => 'ALL',
                'tarif' => 400000.00,
                'status' => 'aktif',
                'keterangan' => 'Tarif LOLO Pelindo Batam 20ft',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'vendor' => 'Pelindo Batam',
                'nama_biaya' => 'Biaya LOLO Pelindo Batam 40ft',
                'kegiatan' => 'LOLO Batam',
                'size' => '40',
                'tipe' => 'ALL',
                'tarif' => 700000.00,
                'status' => 'aktif',
                'keterangan' => 'Tarif LOLO Pelindo Batam 40ft',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('master_pricelist_lolo_batams')->insert($seeds);
    }

    public function down(): void
    {
        Schema::dropIfExists('master_pricelist_lolo_batams');
    }
};
