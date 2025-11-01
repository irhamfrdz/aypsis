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
        Schema::table('pranota_uang_keneks', function (Blueprint $table) {
            // Remove individual surat jalan fields as we'll store multiple surat jalans
            $table->dropForeign(['surat_jalan_id']);
            $table->dropColumn([
                'surat_jalan_id',
                'no_surat_jalan', 
                'supir_nama',
                'kenek_nama',
                'no_plat',
                'uang_rit_kenek',
                'total_rit'
            ]);
            
            // Add new fields for summary data
            $table->integer('jumlah_surat_jalan')->default(0)->after('tanggal');
            $table->integer('jumlah_kenek')->default(0)->after('jumlah_surat_jalan');
            $table->decimal('total_uang_kenek', 15, 2)->default(0)->after('jumlah_kenek');
            $table->decimal('total_hutang', 15, 2)->default(0)->after('total_uang_kenek');
            $table->decimal('total_tabungan', 15, 2)->default(0)->after('total_hutang');
            $table->decimal('grand_total', 15, 2)->default(0)->after('total_tabungan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_uang_keneks', function (Blueprint $table) {
            // Remove new fields
            $table->dropColumn([
                'jumlah_surat_jalan',
                'jumlah_kenek', 
                'total_uang_kenek',
                'total_hutang',
                'total_tabungan',
                'grand_total'
            ]);
            
            // Add back original fields
            $table->unsignedBigInteger('surat_jalan_id')->after('tanggal');
            $table->string('no_surat_jalan')->after('surat_jalan_id');
            $table->string('supir_nama')->after('no_surat_jalan');
            $table->string('kenek_nama')->after('supir_nama');
            $table->string('no_plat')->after('kenek_nama');
            $table->decimal('uang_rit_kenek', 15, 2)->default(50000)->after('no_plat');
            $table->decimal('total_rit', 15, 2)->default(0)->after('uang_rit_kenek');
            
            // Add back foreign key
            $table->foreign('surat_jalan_id')->references('id')->on('surat_jalans')->onDelete('cascade');
        });
    }
};
