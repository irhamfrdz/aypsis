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
        // Modify the status enum to include 'belum masuk checkpoint'
        DB::statement("ALTER TABLE surat_jalans MODIFY COLUMN status ENUM('draft', 'active', 'completed', 'cancelled', 'belum masuk checkpoint') DEFAULT 'belum masuk checkpoint'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE surat_jalans MODIFY COLUMN status ENUM('draft', 'active', 'completed', 'cancelled') DEFAULT 'draft'");
    }
};
