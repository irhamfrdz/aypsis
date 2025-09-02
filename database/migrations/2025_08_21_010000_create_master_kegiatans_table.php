<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('master_kegiatans')) {
            Schema::create('master_kegiatans', function (Blueprint $table) {
                $table->id();
                $table->string('kode')->unique();
                $table->string('kegiatan');
                $table->text('keterangan')->nullable();
                $table->enum('status', ['aktif','nonaktif'])->default('aktif');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('master_kegiatans');
    }
};
