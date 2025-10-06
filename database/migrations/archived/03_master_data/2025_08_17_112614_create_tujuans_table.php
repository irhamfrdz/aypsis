<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tujuans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tujuan')->unique();
            $table->text('deskripsi')->nullable();
            $table->decimal('uang_jalan', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tujuans');
    }
};
