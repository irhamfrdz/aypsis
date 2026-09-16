<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wa_phone_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('shipper_name')->unique()->comment('Nama shipper (key unik)');
            $table->string('telepon')->nullable()->comment('Nomor WA yang disimpan manual oleh user');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('User yang terakhir menyimpan');
            $table->timestamps();

            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_phone_overrides');
    }
};
