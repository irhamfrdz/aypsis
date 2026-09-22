<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('permohonan_amprahans')) {
            return;
        }

        Schema::table('permohonan_amprahans', function (Blueprint $table) {
            $table->unsignedBigInteger('kapal_id')->nullable()->change();
            $table->string('nomor_voyage')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('permohonan_amprahans')) {
            return;
        }

        Schema::table('permohonan_amprahans', function (Blueprint $table) {
            $table->unsignedBigInteger('kapal_id')->nullable(false)->change();
            $table->string('nomor_voyage')->nullable(false)->change();
        });
    }
};
