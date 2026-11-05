<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            $table->index(['lokasi', 'tanggal_surat_jalan'], 'sj_bongkaran_lokasi_tanggal_index');
        });

        foreach (['view', 'create', 'update', 'delete'] as $action) {
            DB::table('permissions')->updateOrInsert(
                ['name' => "surat-jalan-bongkaran-tanjung-pinang-{$action}"],
                ['description' => ucfirst($action).' Surat Jalan Bongkaran Tanjung Pinang', 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('name', array_map(
            fn ($action) => "surat-jalan-bongkaran-tanjung-pinang-{$action}",
            ['view', 'create', 'update', 'delete']
        ))->delete();

        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            $table->dropIndex('sj_bongkaran_lokasi_tanggal_index');
        });
    }
};
