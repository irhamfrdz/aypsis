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
        Schema::table('tanda_terimas', function (Blueprint $table) {
            // Cek dan tambahkan field yang belum ada
            if (!Schema::hasColumn('tanda_terimas', 'nomor_ro')) {
                $table->string('nomor_ro')->nullable()->after('estimasi_nama_kapal');
            }
            
            if (!Schema::hasColumn('tanda_terimas', 'tanggal_checkpoint_supir')) {
                $table->date('tanggal_checkpoint_supir')->nullable()->after('tanggal_surat_jalan');
            }
            
            if (!Schema::hasColumn('tanda_terimas', 'supir_pengganti')) {
                $table->string('supir_pengganti')->nullable()->after('supir');
            }
            
            if (!Schema::hasColumn('tanda_terimas', 'no_plat')) {
                $table->string('no_plat')->nullable()->after('supir_pengganti');
            }
            
            // Dimensi dan volume - akan disimpan sebagai JSON untuk multiple entries
            if (!Schema::hasColumn('tanda_terimas', 'dimensi_details')) {
                $table->json('dimensi_details')->nullable()->after('dimensi');
            }
            
            // Field untuk multiple kontainer details
            if (!Schema::hasColumn('tanda_terimas', 'kontainer_details')) {
                $table->json('kontainer_details')->nullable()->after('no_kontainer');
            }
            
            if (!Schema::hasColumn('tanda_terimas', 'tipe_kontainer_details')) {
                $table->json('tipe_kontainer_details')->nullable()->after('tipe_kontainer');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terimas', function (Blueprint $table) {
            $columns = [
                'nomor_ro',
                'tanggal_checkpoint_supir',
                'supir_pengganti',
                'no_plat',
                'dimensi_details',
                'kontainer_details',
                'tipe_kontainer_details'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('tanda_terimas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
