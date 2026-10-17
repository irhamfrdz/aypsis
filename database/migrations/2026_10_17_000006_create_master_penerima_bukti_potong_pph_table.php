<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_penerima_bukti_potong_pph', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('npwp', 32)->nullable();
            $table->string('nik', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('email')->nullable();
            $table->string('telepon', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('nama');
        });

        $now = now();
        DB::table('permissions')->insertOrIgnore([
            ['name' => 'master-penerima-bukti-potong-pph-view', 'description' => 'Melihat master penerima bukti potong PPh', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'master-penerima-bukti-potong-pph-create', 'description' => 'Menambah penerima bukti potong PPh', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'master-penerima-bukti-potong-pph-update', 'description' => 'Mengubah penerima bukti potong PPh', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'master-penerima-bukti-potong-pph-delete', 'description' => 'Menghapus penerima bukti potong PPh', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('master_penerima_bukti_potong_pph');

        DB::table('permissions')->whereIn('name', [
            'master-penerima-bukti-potong-pph-view',
            'master-penerima-bukti-potong-pph-create',
            'master-penerima-bukti-potong-pph-update',
            'master-penerima-bukti-potong-pph-delete',
        ])->delete();
    }
};
