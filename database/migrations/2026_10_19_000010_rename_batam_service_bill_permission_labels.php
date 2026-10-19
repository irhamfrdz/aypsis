<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const LABELS = [
        'tagihan-service-kendaraan-batam-view' => [
            'old' => 'Melihat Tagihan Service Kendaraan Batam',
            'new' => 'Melihat Tagihan Lain Lain Batam',
        ],
        'tagihan-service-kendaraan-batam-create' => [
            'old' => 'Menambah Tagihan Service Kendaraan Batam',
            'new' => 'Menambah Tagihan Lain Lain Batam',
        ],
        'tagihan-service-kendaraan-batam-update' => [
            'old' => 'Mengubah Tagihan Service Kendaraan Batam',
            'new' => 'Mengubah Tagihan Lain Lain Batam',
        ],
        'tagihan-service-kendaraan-batam-delete' => [
            'old' => 'Menghapus Tagihan Service Kendaraan Batam',
            'new' => 'Menghapus Tagihan Lain Lain Batam',
        ],
    ];

    public function up(): void
    {
        if (Schema::hasTable('tagihan_service_kendaraan_batams') && ! Schema::hasTable('tagihan_lain_lain_batams')) {
            Schema::rename('tagihan_service_kendaraan_batams', 'tagihan_lain_lain_batams');
        }

        if (Schema::hasTable('tagihan_service_kendaraan_batam_sequences') && ! Schema::hasTable('tagihan_lain_lain_batam_sequences')) {
            Schema::rename('tagihan_service_kendaraan_batam_sequences', 'tagihan_lain_lain_batam_sequences');
        }

        foreach (self::LABELS as $permission => $labels) {
            DB::table('permissions')
                ->where('name', $permission)
                ->update(['description' => $labels['new'], 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        foreach (self::LABELS as $permission => $labels) {
            DB::table('permissions')
                ->where('name', $permission)
                ->update(['description' => $labels['old'], 'updated_at' => now()]);
        }

        if (Schema::hasTable('tagihan_lain_lain_batams') && ! Schema::hasTable('tagihan_service_kendaraan_batams')) {
            Schema::rename('tagihan_lain_lain_batams', 'tagihan_service_kendaraan_batams');
        }

        if (Schema::hasTable('tagihan_lain_lain_batam_sequences') && ! Schema::hasTable('tagihan_service_kendaraan_batam_sequences')) {
            Schema::rename('tagihan_lain_lain_batam_sequences', 'tagihan_service_kendaraan_batam_sequences');
        }
    }
};
