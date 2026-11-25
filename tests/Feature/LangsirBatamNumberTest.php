<?php

namespace Tests\Feature;

use App\Models\LangsirBatam;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LangsirBatamNumberTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->travelTo(now()->setDate(2026, 11, 25)->startOfDay());
        Schema::create('langsir_batams', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
        (require database_path('migrations/2026_11_25_140000_add_operator_type_to_langsir_batams.php'))->up();
    }

    protected function tearDown(): void
    {
        $this->travelBack();
        parent::tearDown();
    }

    public function test_deleted_number_is_not_reused(): void
    {
        LangsirBatam::create(['no_transaksi' => 'LNG-20261125-0183']);
        LangsirBatam::create(['no_transaksi' => 'LNG-20261125-0184'])->delete();
        $this->assertSame('LNG-20261125-0185', LangsirBatam::generateNoTransaksi());
        $this->assertSame('LNG-20261125-0185', LangsirBatam::createWithAutomaticNumber([])->no_transaksi);
    }

    public function test_stale_form_number_is_replaced_when_saving(): void
    {
        $preview = LangsirBatam::generateNoTransaksi();
        $first = LangsirBatam::createWithAutomaticNumber(['no_transaksi' => $preview]);
        $second = LangsirBatam::createWithAutomaticNumber(['no_transaksi' => $preview]);
        $this->assertSame('LNG-20261125-0001', $first->no_transaksi);
        $this->assertSame('LNG-20261125-0002', $second->no_transaksi);
        $this->assertDatabaseCount('langsir_batams', 2);
    }

    public function test_sequence_is_numeric_after_four_digits_and_resets_daily(): void
    {
        foreach (['LNG-20261125-9999', 'LNG-20261125-10000', 'LNG-20261124-99999'] as $number) {
            LangsirBatam::create(['no_transaksi' => $number]);
        }
        $this->assertSame('LNG-20261125-10001', LangsirBatam::createWithAutomaticNumber([])->no_transaksi);
        $this->travelTo(now()->addDay());
        $this->assertSame('LNG-20261126-0001', LangsirBatam::createWithAutomaticNumber([])->no_transaksi);
    }

    public function test_store_accepts_a_stale_number_and_history_uses_the_saved_number(): void
    {
        Schema::table('langsir_batams', function (Blueprint $table) {
            foreach (['tanggal', 'no_kontainer', 'size', 'dari', 'ke', 'sumber_chasis', 'no_chasis', 'supir', 'keterangan', 'status'] as $column) {
                $table->string($column)->nullable();
            }
            foreach (['gudang_tujuan_id', 'chasis_mobil_id', 'input_by', 'operator_lolo_1_id', 'operator_lolo_2_id'] as $column) {
                $table->unsignedBigInteger($column)->nullable();
            }
            $table->decimal('biaya')->nullable();
            $table->decimal('biaya_lolo')->nullable();
            $table->boolean('menggunakan_lolo')->default(false);
            $table->boolean('ob_dalam_pelabuhan')->default(false);
        });
        Schema::create('gudangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_gudang');
        });
        Schema::create('stock_kontainers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_seri_gabungan');
            $table->string('status')->nullable();
        });
        Schema::create('history_kontainers', function (Blueprint $table) {
            $table->id();
            foreach (['nomor_kontainer', 'tipe_kontainer', 'jenis_kegiatan', 'tanggal_kegiatan', 'keterangan'] as $column) {
                $table->text($column);
            }
            foreach (['asal_gudang_id', 'gudang_id', 'created_by'] as $column) {
                $table->unsignedBigInteger($column)->nullable();
            }
            $table->timestamps();
        });
        DB::table('gudangs')->insert(['id' => 1, 'nama_gudang' => 'GUDANG UJI']);
        LangsirBatam::create(['no_transaksi' => 'LNG-20261125-0184']);
        $request = \Illuminate\Http\Request::create('/langsir-batam', 'POST', [
            'no_transaksi' => 'LNG-20261125-0184', 'tanggal' => '2026-11-25',
            'no_kontainer' => 'TEST123', 'size' => '20FT', 'dari' => 'GUDANG UJI', 'ke' => 'GUDANG UJI',
            'sumber_chasis' => 'PB', 'no_chasis_pb' => 'PB-01', 'biaya' => 50000,
            'status' => 'FULL', 'gudang_tujuan_id' => 1, 'menggunakan_lolo' => 'tidak', 'keterangan' => null,
        ]);
        $response = \Illuminate\Database\Eloquent\Model::withoutEvents(
            fn () => app(\App\Http\Controllers\LangsirBatamController::class)->store($request)
        );
        $this->assertSame(302, $response->getStatusCode());
        $this->assertDatabaseHas('langsir_batams', ['no_transaksi' => 'LNG-20261125-0185', 'no_kontainer' => 'TEST123']);
        $this->assertStringContainsString('[No Transaksi: LNG-20261125-0185]', DB::table('history_kontainers')->value('keterangan'));
        Schema::create('karyawans', fn (Blueprint $table) => $table->id());
        DB::table('karyawans')->insert([['id' => 10], ['id' => 11]]);
        $vendorRequest = \Illuminate\Http\Request::create('/langsir-batam', 'POST', array_replace($request->all(), [
            'menggunakan_lolo' => 'ya', 'tipe_operator_lolo' => 'VENDOR', 'operator_lolo_vendor' => 'PT LOLO BATAM',
            'no_kontainer' => 'VENDOR123', 'biaya_lolo' => '100.000',
        ]));
        \Illuminate\Database\Eloquent\Model::withoutEvents(fn () => app(\App\Http\Controllers\LangsirBatamController::class)->store($vendorRequest));
        $this->assertDatabaseHas('langsir_batams', ['no_kontainer' => 'VENDOR123', 'tipe_operator_lolo' => 'VENDOR', 'operator_lolo_vendor' => 'PT LOLO BATAM', 'operator_lolo_1_id' => null, 'operator_lolo_2_id' => null, 'biaya_lolo' => 100000]);
        $aypRequest = \Illuminate\Http\Request::create('/langsir-batam', 'POST', array_replace($request->all(), [
            'menggunakan_lolo' => 'ya', 'tipe_operator_lolo' => 'AYP', 'no_kontainer' => 'AYP123',
            'operator_lolo_1_id' => 10, 'operator_lolo_2_id' => 11,
        ]));
        \Illuminate\Database\Eloquent\Model::withoutEvents(fn () => app(\App\Http\Controllers\LangsirBatamController::class)->store($aypRequest));
        $this->assertDatabaseHas('langsir_batams', ['no_kontainer' => 'AYP123', 'tipe_operator_lolo' => 'AYP', 'operator_lolo_1_id' => 10, 'operator_lolo_2_id' => 11, 'operator_lolo_vendor' => null]);
        foreach ([['tipe_operator_lolo' => null], ['tipe_operator_lolo' => 'VENDOR', 'operator_lolo_vendor' => ''], ['tipe_operator_lolo' => 'AYP'], ['tipe_operator_lolo' => 'AYP', 'operator_lolo_1_id' => 10, 'operator_lolo_2_id' => 10]] as $invalid) {
            try {
                app(\App\Http\Controllers\LangsirBatamController::class)->store(\Illuminate\Http\Request::create('/', 'POST', array_replace($request->all(), ['menggunakan_lolo' => 'ya'], $invalid)));
                $this->fail('Incomplete operator selection should be rejected.');
            } catch (\Illuminate\Validation\ValidationException $e) {
                $this->assertNotEmpty($e->errors());
            }
        }
    }
}
