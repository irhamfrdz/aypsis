<?php

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PembayaranPranotaObAkunMigrationTest extends TestCase
{
    public function test_migration_repairs_a_table_left_by_a_failed_foreign_key(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('akun_coa', fn (Blueprint $table) => $table->id());
        Schema::create('pembayaran_pranota_obs', fn (Blueprint $table) => $table->id());
        Schema::create('pembayaran_pranota_ob_akun_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_pranota_ob_id');
            $table->foreignId('akun_coa_id');
            $table->decimal('nominal', 15, 2);
            $table->timestamps();
        });

        $migration = require database_path('migrations/2026_11_02_000001_create_pembayaran_pranota_ob_akun_items_table.php');
        $migration->up();
        $migration->up();

        $itemKeys = collect(Schema::getForeignKeys('pembayaran_pranota_ob_akun_items'));
        $this->assertTrue($itemKeys->contains(fn ($key) => in_array('pembayaran_pranota_ob_id', $key['columns'])));
        $this->assertTrue($itemKeys->contains(fn ($key) => in_array('akun_coa_id', $key['columns'])));
        $this->assertTrue(Schema::hasColumn('pembayaran_pranota_obs', 'akun_dp_id'));
        $this->assertTrue(collect(Schema::getForeignKeys('pembayaran_pranota_obs'))
            ->contains(fn ($key) => in_array('akun_dp_id', $key['columns'])));

        $migration->down();
        $this->assertFalse(Schema::hasTable('pembayaran_pranota_ob_akun_items'));
        $this->assertFalse(Schema::hasColumn('pembayaran_pranota_obs', 'akun_dp_id'));
    }
}
