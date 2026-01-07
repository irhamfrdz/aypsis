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
        Schema::table('manifests', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['supir_id']);
            
            // Drop columns
            $table->dropColumn([
                'status_bongkar',
                'sudah_ob',
                'sudah_tl',
                'supir_ob',
                'supir_id',
                'tanggal_ob',
                'catatan_ob',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->enum('status_bongkar', ['Sudah Bongkar', 'Belum Bongkar'])->default('Belum Bongkar')->after('id');
            $table->boolean('sudah_ob')->default(false)->after('status_bongkar');
            $table->boolean('sudah_tl')->default(false)->after('sudah_ob');
            $table->string('supir_ob')->nullable()->after('penerimaan');
            $table->unsignedBigInteger('supir_id')->nullable()->after('updated_at')->index();
            $table->timestamp('tanggal_ob')->nullable()->after('supir_id');
            $table->text('catatan_ob')->nullable()->after('tanggal_ob');
            
            $table->foreign('supir_id')->references('id')->on('karyawans')->onDelete('set null');
        });
    }
};
