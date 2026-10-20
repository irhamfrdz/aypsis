<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PembayaranPranotaCatAkunMigrationTest extends TestCase
{
    public function test_existing_payments_use_the_previous_cat_cost_account(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_akun');
        });
        Schema::create('pembayaran_pranota_cat', function (Blueprint $table) {
            $table->id();
        });

        DB::table('akun_coa')->insert(['id' => 7, 'nama_akun' => 'Biaya CAT Kontainer']);
        DB::table('pembayaran_pranota_cat')->insert(['id' => 1]);

        $migration = require database_path('migrations/2026_10_20_000003_add_akun_coa_id_to_pembayaran_pranota_cat.php');
        $migration->up();

        $this->assertDatabaseHas('pembayaran_pranota_cat', ['id' => 1, 'akun_coa_id' => 7]);
    }
}
