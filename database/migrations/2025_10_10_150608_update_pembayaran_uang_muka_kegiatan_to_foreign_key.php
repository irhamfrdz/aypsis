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
        Schema::table('pembayaran_uang_muka', function (Blueprint $table) {
            // Drop index first
            $table->dropIndex(['kegiatan']);

            // Change kegiatan column to unsigned big integer
            $table->unsignedBigInteger('kegiatan')->change();

            // Add foreign key constraint
            $table->foreign('kegiatan')->references('id')->on('master_kegiatans');

            // Add new index
            $table->index('kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_uang_muka', function (Blueprint $table) {
            // Drop foreign key and index
            $table->dropForeign(['kegiatan']);
            $table->dropIndex(['kegiatan']);

            // Change back to string
            $table->string('kegiatan')->change();

            // Add back the string index
            $table->index('kegiatan');
        });
    }
};
