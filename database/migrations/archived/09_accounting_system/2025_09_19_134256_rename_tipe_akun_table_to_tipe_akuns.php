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
        Schema::rename('tipe_akun', 'tipe_akuns');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('tipe_akuns', 'tipe_akun');
    }
};
