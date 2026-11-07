<?php

namespace Tests\Feature;

use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AssetController;
use App\Models\Asset;
use App\Models\Karyawan;
use App\Models\KaryawanApdTransaction;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class KaryawanApdTransactionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id(); $table->string('nama_lengkap')->nullable(); $table->string('nik')->nullable();
        });
        DB::table('karyawans')->insert(['id' => 7, 'nama_lengkap' => 'Aan', 'nik' => '1679']);
        Schema::create('assets', function (Blueprint $table) {
            $table->id(); $table->string('nama_asset'); $table->string('kategori');
            $table->string('nomor_seri')->nullable(); $table->text('nomor_seri_items')->nullable(); $table->decimal('kuantitas', 12, 2)->nullable(); $table->unsignedBigInteger('karyawan_id')->nullable(); $table->softDeletes();
        });
        Schema::create('karyawan_apd_transactions', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('karyawan_id'); $table->unsignedBigInteger('asset_id');
            $table->date('tanggal_serah_terima'); $table->string('nama_barang'); $table->string('ukuran')->nullable();
            $table->decimal('qty', 12, 2); $table->text('keterangan')->nullable(); $table->string('status')->default('Di Gunakan'); $table->boolean('stock_deducted')->default(false); $table->timestamps();
        });
        Schema::create('apd_return_histories', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('asset_id'); $table->unsignedBigInteger('transaction_id')->unique();
            $table->string('nomor_transaksi'); $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->string('nama_karyawan')->nullable(); $table->string('nik')->nullable(); $table->string('nama_barang');
            $table->string('ukuran')->nullable(); $table->decimal('qty', 12, 2); $table->unsignedBigInteger('returned_by')->nullable();
            $table->string('nama_pengguna')->nullable(); $table->timestamp('returned_at');
        });
        DB::table('assets')->insert([
            ['id' => 1, 'nama_asset' => 'Helm', 'kategori' => 'APD', 'nomor_seri_items' => json_encode([['nomor' => 'H1', 'ukuran' => 'L']]), 'kuantitas' => 5],
            ['id' => 2, 'nama_asset' => 'Laptop', 'kategori' => 'Elektronik', 'nomor_seri_items' => null, 'kuantitas' => 5],
        ]);
    }

    public function test_print_receipt_uses_selected_employee_assets_and_rejects_other_employees_assets(): void
    {
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('karyawan_id'); $table->boolean('is_asset_karyawan');
            $table->string('kode_no'); $table->string('catatan'); $table->string('kategori_asset');
        });
        DB::table('kendaraans')->insert([
            ['id' => 1, 'karyawan_id' => 7, 'is_asset_karyawan' => 1, 'kode_no' => 'MOTOR-1', 'catatan' => 'Motor', 'kategori_asset' => 'Kendaraan'],
            ['id' => 2, 'karyawan_id' => 8, 'is_asset_karyawan' => 1, 'kode_no' => 'MOTOR-2', 'catatan' => 'Motor lain', 'kategori_asset' => 'Kendaraan'],
        ]);
        KaryawanApdTransaction::create(['karyawan_id' => 7, 'asset_id' => 1, 'tanggal_serah_terima' => '2026-11-06', 'nama_barang' => 'Helm', 'qty' => 2]);
        $employee = Karyawan::findOrFail(7);
        $controller = new KaryawanController;
        $view = $controller->printAssetTandaTerima(Request::create('/', 'POST', ['selected' => ['apd:1', 'used:kendaraans:1']]), $employee);
        $this->assertCount(2, $view->getData()['receiptItems']);
        $html = $view->render();
        $this->assertStringContainsString('Helm', $html);
        $this->assertStringContainsString('MOTOR-1', $html);
        $this->assertStringNotContainsString('MOTOR-2', $html);
        try {
            $controller->printAssetTandaTerima(Request::create('/', 'POST', ['selected' => ['used:kendaraans:2']]), $employee);
            $this->fail('Expected unauthorized asset to be rejected.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    public function test_records_apd_with_unique_transaction_number_and_selected_size(): void
    {
        $employee = new Karyawan; $employee->id = 7;
        $controller = new KaryawanController;
        foreach ([1, 2] as $qty) {
            $controller->storeApd(Request::create('/', 'POST', ['asset_id' => 1, 'tanggal_serah_terima' => '2026-11-06', 'ukuran' => 'L', 'qty' => $qty]), $employee);
        }
        $rows = KaryawanApdTransaction::orderBy('id')->get();
        $this->assertSame(['TRX-APD-001', 'TRX-APD-002'], $rows->map->nomor_transaksi->all());
        $this->assertSame('Helm', $rows[0]->nama_barang);
        $this->assertSame('L', $rows[0]->ukuran);
        $this->assertSame('Di Gunakan', $rows[0]->status);
        $this->assertSame(7, $rows[0]->karyawan_id);
        $this->assertEquals(2, DB::table('assets')->where('id', 1)->value('kuantitas'));
    }

    public function test_rejects_non_apd_assets_and_sizes_not_on_selected_asset(): void
    {
        $employee = new Karyawan; $employee->id = 7;
        foreach ([['asset_id' => 2, 'ukuran' => ''], ['asset_id' => 1, 'ukuran' => 'XL']] as $input) {
            try {
                (new KaryawanController)->storeApd(Request::create('/', 'POST', $input + ['tanggal_serah_terima' => '2026-11-06', 'qty' => 1]), $employee);
                $this->fail('Expected validation failure.');
            } catch (ValidationException $exception) {
                $this->assertNotEmpty($exception->errors());
            }
        }
        $this->assertSame(0, KaryawanApdTransaction::count());
        $this->assertEquals(5, DB::table('assets')->where('id', 1)->value('kuantitas'));
    }

    public function test_insufficient_stock_does_not_create_transaction_or_change_stock(): void
    {
        $employee = new Karyawan; $employee->id = 7;
        try {
            (new KaryawanController)->storeApd(Request::create('/', 'POST', ['asset_id' => 1, 'tanggal_serah_terima' => '2026-11-06', 'ukuran' => 'L', 'qty' => 6]), $employee);
            $this->fail('Expected insufficient stock validation.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('qty', $exception->errors());
        }
        $this->assertSame(0, KaryawanApdTransaction::count());
        $this->assertEquals(5, DB::table('assets')->where('id', 1)->value('kuantitas'));
    }

    public function test_return_restores_recorded_stock_only_once(): void
    {
        $employee = new Karyawan; $employee->id = 7;
        (new KaryawanController)->storeApd(Request::create('/', 'POST', ['asset_id' => 1, 'tanggal_serah_terima' => '2026-11-06', 'ukuran' => 'L', 'qty' => 2]), $employee);
        $record = KaryawanApdTransaction::first();
        $request = Request::create('/', 'POST');
        $request->setUserResolver(fn () => new class { public function hasRole($role) { return true; } });
        $controller = new AssetController;
        $controller->returnEmployeeApd($request, Asset::findOrFail(1), $record);
        $controller->returnEmployeeApd($request, Asset::findOrFail(1), $record);
        $this->assertEquals(5, DB::table('assets')->where('id', 1)->value('kuantitas'));
        $this->assertSame('Dikembalikan', $record->fresh()->status);
        $this->assertSame(1, DB::table('apd_return_histories')->count());
        $this->assertSame('Aan', DB::table('apd_return_histories')->value('nama_karyawan'));
        $controller->deleteEmployeeApd($request, Asset::findOrFail(1), $record);
        $this->assertSame(1, DB::table('apd_return_histories')->count());
    }

    public function test_return_of_legacy_transaction_restores_stock(): void
    {
        $record = KaryawanApdTransaction::create(['karyawan_id' => 7, 'asset_id' => 1, 'tanggal_serah_terima' => '2026-11-06', 'nama_barang' => 'Helm', 'qty' => 1]);
        $request = Request::create('/', 'POST');
        $request->setUserResolver(fn () => new class { public function hasRole($role) { return true; } });
        (new AssetController)->returnEmployeeApd($request, Asset::findOrFail(1), $record);
        $this->assertEquals(6, DB::table('assets')->where('id', 1)->value('kuantitas'));
        $this->assertSame('Dikembalikan', $record->fresh()->status);
    }

    public function test_deleting_history_does_not_change_stock(): void
    {
        $record = KaryawanApdTransaction::create(['karyawan_id' => 7, 'asset_id' => 1, 'tanggal_serah_terima' => '2026-11-06', 'nama_barang' => 'Helm', 'qty' => 1]);
        $request = Request::create('/', 'DELETE');
        $request->setUserResolver(fn () => new class { public function hasRole($role) { return true; } });
        (new AssetController)->deleteEmployeeApd($request, Asset::findOrFail(1), $record);
        $this->assertSame(0, KaryawanApdTransaction::count());
        $this->assertEquals(5, DB::table('assets')->where('id', 1)->value('kuantitas'));
    }
}
