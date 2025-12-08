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
        Schema::table('naik_kapal', function (Blueprint $table) {
            $table->unsignedBigInteger('supir_id')->nullable()->after('sudah_ob');
            $table->timestamp('tanggal_ob')->nullable()->after('supir_id');
            $table->text('catatan_ob')->nullable()->after('tanggal_ob');
            
            // Add foreign key constraint
            $table->foreign('supir_id')->references('id')->on('karyawans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('naik_kapal', function (Blueprint $table) {
            $table->dropForeign(['supir_id']);
            $table->dropColumn(['supir_id', 'tanggal_ob', 'catatan_ob']);
        });
    }
};
