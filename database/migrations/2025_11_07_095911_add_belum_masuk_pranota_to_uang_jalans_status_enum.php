<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'belum_masuk_pranota' to the status enum
        DB::statement("ALTER TABLE uang_jalans MODIFY COLUMN status ENUM('belum_dibayar', 'belum_masuk_pranota', 'sudah_masuk_pranota', 'lunas', 'dibatalkan') DEFAULT 'belum_masuk_pranota'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'belum_masuk_pranota' from the status enum
        DB::statement("ALTER TABLE uang_jalans MODIFY COLUMN status ENUM('belum_dibayar', 'sudah_masuk_pranota', 'lunas', 'dibatalkan') DEFAULT 'belum_dibayar'");
    }
};
