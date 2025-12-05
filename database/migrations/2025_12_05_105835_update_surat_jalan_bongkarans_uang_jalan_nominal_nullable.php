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
        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            // Change uang_jalan_nominal column to be nullable
            $table->decimal('uang_jalan_nominal', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            // Revert uang_jalan_nominal column to be NOT NULL
            $table->decimal('uang_jalan_nominal', 15, 2)->nullable(false)->change();
        });
    }
};
