<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_deletion_marks', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 50);
            $table->dateTime('waktu');
            $table->string('tipe', 50);
            $table->timestamps();

            $table->unique(['nik', 'waktu', 'tipe']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_deletion_marks');
    }
};
