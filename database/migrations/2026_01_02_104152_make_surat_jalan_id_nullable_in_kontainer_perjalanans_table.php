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
        Schema::table('kontainer_perjalanans', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['surat_jalan_id']);
            
            // Make column nullable
            $table->unsignedBigInteger('surat_jalan_id')->nullable()->change();
            
            // Re-add foreign key as nullable
            $table->foreign('surat_jalan_id')
                  ->references('id')
                  ->on('surat_jalans')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontainer_perjalanans', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['surat_jalan_id']);
            
            // Make column NOT nullable
            $table->unsignedBigInteger('surat_jalan_id')->nullable(false)->change();
            
            // Re-add foreign key
            $table->foreign('surat_jalan_id')
                  ->references('id')
                  ->on('surat_jalans')
                  ->onDelete('cascade');
        });
    }
};
