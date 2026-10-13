<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RemovePenyewaanKontainerTest extends TestCase
{
    public function test_removal_drops_module_tables_in_foreign_key_order_and_preserves_existing_modules(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => true,
        ]);
        DB::purge('sqlite');

        (require database_path('migrations/2026_06_29_112846_create_sk_react_tables.php'))->up();
        Schema::create('vendor_kontainer_sewas', fn (Blueprint $table) => $table->id());
        DB::table('vendor_kontainer_sewas')->insert(['id' => 1]);
        DB::table('sk_customers')->insert(['id_customer' => 'customer-1', 'nama_customer' => 'Customer']);
        DB::table('sk_tipes')->insert(['id_tipe' => 'tipe-1', 'nama_tipe' => 'Dry']);
        DB::table('sk_ukurans')->insert(['id_ukuran' => 'ukuran-1', 'deskripsi_ukuran' => '20 feet']);
        DB::table('sk_kontainers')->insert([
            'no_kontainer' => 'TEST1234567', 'id_customer' => 'customer-1',
            'id_tipe' => 'tipe-1', 'id_ukuran' => 'ukuran-1',
        ]);
        DB::table('sk_tarifs')->insert([
            'id_tarif' => 'tarif-1', 'id_customer' => 'customer-1',
            'id_tipe' => 'tipe-1', 'id_ukuran' => 'ukuran-1',
            'tarif_bulanan' => 100, 'tarif_harian' => 10, 'tanggal_mulai_berlaku' => '2026-10-01',
        ]);
        DB::table('sk_sewas')->insert([
            'id_sewa' => 'sewa-1', 'no_kontainer' => 'TEST1234567', 'id_customer' => 'customer-1',
            'tanggal_sewa' => '2026-10-01', 'tarif_bulanan' => 100, 'tarif_harian' => 10,
            'jenis_tarif' => 'Bulanan', 'status_sewa' => 'Aktif',
        ]);
        DB::table('sk_tagihan_bulans')->insert(['id_tagihan' => 'tagihan-1', 'id_sewa' => 'sewa-1']);
        DB::table('sk_invoice_grups')->insert([
            'nomor_invoice' => 'invoice-1', 'id_customer' => 'customer-1',
            'tanggal_invoice' => '2026-10-01', 'status_pembayaran' => 'Belum Bayar',
            'deskripsi' => 'Invoice', 'list_id_tagihan' => '["tagihan-1"]',
        ]);
        Schema::create('sk_invoice_grup_tagihans', function (Blueprint $table) {
            $table->string('nomor_invoice');
            $table->foreign('nomor_invoice')->references('nomor_invoice')->on('sk_invoice_grups');
        });
        DB::table('sk_invoice_grup_tagihans')->insert(['nomor_invoice' => 'invoice-1']);
        foreach (['sk_tarif_sewas', 'sk_ukuran_kontainers', 'sk_tipe_kontainers'] as $legacyTable) {
            Schema::create($legacyTable, fn (Blueprint $table) => $table->id());
        }

        $migration = require database_path('migrations/2026_10_13_010000_remove_penyewaan_kontainer_app.php');
        $migration->up();
        $migration->up();

        $this->assertSame(['vendor_kontainer_sewas'], Schema::getTableListing());
        $this->assertSame(1, DB::table('vendor_kontainer_sewas')->count());
    }

    public function test_module_routes_and_public_application_are_removed(): void
    {
        $moduleRoutes = array_filter(
            app('router')->getRoutes()->getRoutes(),
            fn ($route) => in_array($route->uri(), ['open-penyewaan-kontainer', 'api/sewa-kontainer/sync'])
        );

        $this->assertSame([], array_values($moduleRoutes));
        $this->assertDirectoryDoesNotExist(public_path('penyewaan-app'));
    }
}
