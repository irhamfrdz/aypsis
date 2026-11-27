<?php

namespace Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait CreatesSharedShippingDpSchema
{
    private function createOtherShippingDpTables(string $source): void
    {
        Schema::create("biaya_kapal_$source", function (Blueprint $table) use ($source) {
            $table->id();
            $table->foreignId('biaya_kapal_id');
            foreach (['kapal', 'voyage', 'jenis_biaya', 'nomor_kontainer', 'nomor_referensi', 'lokasi', 'size', 'penerima', 'nomor_rekening', 'keterangan'] as $field) {
                $table->string($field)->nullable();
            }
            $table->unsignedBigInteger('bl_id')->nullable();
            $table->unsignedBigInteger("pricelist_{$source}_id")->nullable();
            $table->date('tanggal_invoice_vendor')->nullable();
            foreach (['kuantitas', 'harga', 'sub_total', 'pph', 'ppn', 'biaya_materai', 'adjustment', 'biaya_admin', 'grand_total'] as $field) {
                $table->decimal($field, 15, 2)->default(0);
            }
            foreach (['pph_active', 'ppn_active', 'is_muat', 'is_bongkar'] as $field) {
                $table->boolean($field)->default(false);
            }
            if ($source === 'temas') {
                $table->string('nomor_bl')->nullable();
                $table->boolean('is_per_container')->nullable();
            }
            $table->timestamps();
        });
        $migrations = $source === 'meratus' ? ['2026_10_24_090000_add_meratus_billing_stages.php'] : [
            '2026_09_22_130000_create_biaya_kapal_temas_stages.php',
            '2026_09_26_120000_add_dp_metadata_to_biaya_kapal_temas_stages.php',
            '2026_10_02_103000_add_nama_bank_to_biaya_kapal_temas_stages.php',
            '2026_10_08_000001_create_temas_dp_references_table.php',
            '2026_10_08_000002_add_used_amount_to_temas_dp_references.php',
        ];
        foreach ($migrations as $migration) {
            (require database_path('migrations/'.$migration))->up();
        }
        (require database_path('migrations/2026_11_26_000001_create_shared_shipping_dp_references.php'))->up();
    }
}
