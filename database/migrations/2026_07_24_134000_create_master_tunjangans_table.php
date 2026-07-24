<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_tunjangans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tunjangan');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'tidak aktif'])->default('aktif');
            $table->timestamps();
        });

        // Insert permission for master-tunjangan
        DB::table('permissions')->insert([
            'name' => 'master-tunjangan',
            'description' => 'Akses menu Master Tunjangan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('permissions')->where('name', 'master-tunjangan')->delete();
        Schema::dropIfExists('master_tunjangans');
    }
};
