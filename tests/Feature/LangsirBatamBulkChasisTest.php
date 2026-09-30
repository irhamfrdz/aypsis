<?php

namespace Tests\Feature;

use App\Http\Controllers\LangsirBatamController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LangsirBatamBulkChasisTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('mobils', function (Blueprint $table) {
            $table->id();
            $table->string('jenis')->nullable();
            $table->string('no_kir')->nullable();
            $table->string('nomor_polisi')->nullable();
        });
        Schema::create('gudangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_gudang');
            $table->string('status');
        });
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('divisi')->nullable();
            $table->string('nama_panggilan')->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->string('plat')->nullable();
        });
        Schema::create('langsir_batams', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi');
            $table->string('no_surat_jalan')->nullable();
            $table->date('tanggal');
            $table->string('no_kontainer');
            $table->string('size');
            $table->string('no_seal')->nullable();
            $table->string('dari');
            $table->string('ke');
            $table->unsignedBigInteger('gudang_tujuan_id')->nullable();
            $table->string('supir')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('sumber_chasis')->nullable();
            $table->unsignedBigInteger('chasis_mobil_id')->nullable();
            $table->string('no_chasis')->nullable();
            $table->decimal('biaya', 15, 2);
            $table->text('keterangan')->nullable();
            $table->string('status');
            $table->boolean('ob_dalam_pelabuhan');
            $table->unsignedBigInteger('input_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('stock_kontainers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_seri_gabungan')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('gudangs_id')->nullable();
        });
        Schema::create('history_kontainers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontainer');
            $table->string('tipe_kontainer');
            $table->string('jenis_kegiatan');
            $table->date('tanggal_kegiatan');
            $table->unsignedBigInteger('asal_gudang_id')->nullable();
            $table->unsignedBigInteger('gudang_id')->nullable();
            $table->text('keterangan');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        DB::table('gudangs')->insert(['id' => 1, 'nama_gudang' => 'Gudang A', 'status' => 'aktif']);
        DB::table('mobils')->insert([
            ['id' => 1, 'jenis' => 'Buntut', 'no_kir' => 'KIR-AYP-01', 'nomor_polisi' => null],
            ['id' => 2, 'jenis' => 'Truck', 'no_kir' => 'KIR-TRUCK-01', 'nomor_polisi' => null],
        ]);
    }

    public function test_bulk_input_resolves_ayp_kir_and_keeps_pb_manual_number(): void
    {
        $response = $this->storeRows([
            $this->row('CONT-AYP', 'AYP', 'kir-ayp-01'),
            $this->row('CONT-PB', 'PB', 'PB-CHASIS-02'),
        ]);

        $this->assertSame(200, $response->getStatusCode(), $response->getContent());
        $this->assertDatabaseHas('langsir_batams', [
            'no_kontainer' => 'CONT-AYP', 'sumber_chasis' => 'AYP',
            'chasis_mobil_id' => 1, 'no_chasis' => 'KIR-AYP-01',
        ]);
        $this->assertDatabaseHas('langsir_batams', [
            'no_kontainer' => 'CONT-PB', 'sumber_chasis' => 'PB',
            'chasis_mobil_id' => null, 'no_chasis' => 'PB-CHASIS-02',
        ]);
    }

    public function test_invalid_ayp_kir_rolls_back_the_entire_batch(): void
    {
        $response = $this->storeRows([
            $this->row('CONT-PB', 'PB', 'PB-CHASIS-02'),
            $this->row('CONT-INVALID', 'AYP', 'KIR-TRUCK-01'),
        ]);

        $this->assertSame(422, $response->getStatusCode(), $response->getContent());
        $this->assertDatabaseCount('langsir_batams', 0);
    }

    private function row(string $container, string $source, string $number): array
    {
        return [
            'tanggal' => '2026-09-30',
            'no_kontainer' => $container,
            'size' => '20FT',
            'dari' => 'PELABUHAN',
            'ke' => 'SRIMAS',
            'gudang_tujuan' => 'Gudang A',
            'biaya' => '50000',
            'status' => 'FULL',
            'sumber_chasis' => $source,
            'no_chasis' => $number,
        ];
    }

    private function storeRows(array $rows)
    {
        return Model::withoutEvents(fn () => (new LangsirBatamController)->storeBulk(
            Request::create('/langsir-batam/store-bulk', 'POST', ['rows' => $rows])
        ));
    }
}
