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
            $table->string('pekerjaan')->nullable();
            $table->date('tanggal_berhenti')->nullable();
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
            $table->boolean('menggunakan_lolo')->default(false);
            $table->unsignedBigInteger('operator_lolo_1_id')->nullable();
            $table->unsignedBigInteger('operator_lolo_2_id')->nullable();
            $table->decimal('biaya_lolo')->default(0);
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
        (require database_path('migrations/2026_11_25_140000_add_operator_type_to_langsir_batams.php'))->up();
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
        Schema::create('kontainers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_seri_gabungan');
        });
        Schema::create('master_chasis_batams', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->softDeletes();
        });
        DB::table('master_chasis_batams')->insert(['kode' => 'KIR-AYP-01']);
        Schema::create('master_pricelist_lolo_batams', function (Blueprint $table) {
            $table->id();
            $table->string('size');
            $table->decimal('tarif');
            $table->string('status');
            $table->softDeletes();
        });
        DB::table('master_pricelist_lolo_batams')->insert(['size' => '20', 'tarif' => 50000, 'status' => 'aktif']);
        DB::table('karyawans')->insert([
            ['id' => 11, 'nama_lengkap' => 'BUDI OPERATOR', 'nama_panggilan' => 'BUDI', 'pekerjaan' => 'OPERATOR FORKLIFT'],
            ['id' => 12, 'nama_lengkap' => 'AGUS OPERATOR', 'nama_panggilan' => 'AGUS', 'pekerjaan' => 'OPERATOR CRANE'],
        ]);
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
            'chasis_mobil_id' => null, 'no_chasis' => 'KIR-AYP-01',
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
        DB::table('kontainers')->updateOrInsert(['nomor_seri_gabungan' => $container]);
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
            'menggunakan_lolo' => 'Tidak',
        ];
    }

    public function test_bulk_lolo_resolves_operators_and_calculates_or_accepts_cost(): void
    {
        $row = $this->row('CONT-LOLO', 'PB', 'PB-01') + ['operator_lolo_1' => 'budi', 'operator_lolo_2' => 'AGUS OPERATOR'];
        $row['menggunakan_lolo'] = 'Ya';
        $manual = array_replace($row, ['no_kontainer' => 'CONT-MANUAL', 'biaya_lolo' => 'Rp 120.000']);
        $this->row('CONT-MANUAL', 'PB', 'PB-02');
        $response = $this->storeRows([$row, $manual]);
        $this->assertSame(200, $response->getStatusCode(), $response->getContent());
        $this->assertDatabaseHas('langsir_batams', ['no_kontainer' => 'CONT-LOLO', 'operator_lolo_1_id' => 11, 'operator_lolo_2_id' => 12, 'biaya_lolo' => 100000]);
        $this->assertDatabaseHas('langsir_batams', ['no_kontainer' => 'CONT-MANUAL', 'biaya_lolo' => 120000]);
    }

    public function test_bad_operators_and_cost_reject_the_entire_batch(): void
    {
        $base = array_replace($this->row('CONT-LOLO', 'PB', 'PB-01'), ['menggunakan_lolo' => 'Ya', 'operator_lolo_1' => 'BUDI', 'operator_lolo_2' => 'AGUS']);
        foreach ([['operator_lolo_2' => 'BUDI OPERATOR'], ['operator_lolo_2' => 'TIDAK ADA'], ['operator_lolo_1' => ''], ['biaya_lolo' => '-100']] as $change) {
            $response = $this->storeRows([$this->row('CONT-OK', 'PB', 'PB-02'), array_replace($base, $change)]);
            $this->assertSame(422, $response->getStatusCode(), $response->getContent());
            $this->assertDatabaseCount('langsir_batams', 0);
        }
    }

    public function test_no_lolo_clears_operators_and_cost(): void
    {
        $response = $this->storeRows([$this->row('CONT-NO', 'PB', 'PB-01') + ['operator_lolo_1' => 'BUDI', 'operator_lolo_2' => 'AGUS', 'biaya_lolo' => 999999]]);
        $this->assertSame(200, $response->getStatusCode(), $response->getContent());
        $this->assertDatabaseHas('langsir_batams', ['no_kontainer' => 'CONT-NO', 'operator_lolo_1_id' => null, 'operator_lolo_2_id' => null, 'biaya_lolo' => 0]);
    }

    public function test_inactive_and_ambiguous_operator_names_are_rejected(): void
    {
        $row = array_replace($this->row('CONT-LOLO', 'PB', 'PB-01'), ['menggunakan_lolo' => 'Ya', 'operator_lolo_1' => 'BUDI', 'operator_lolo_2' => 'AGUS']);
        DB::table('karyawans')->where('id', 12)->update(['tanggal_berhenti' => '2026-01-01']);
        $this->assertSame(422, $this->storeRows([$row])->getStatusCode());
        DB::table('karyawans')->where('id', 12)->update(['tanggal_berhenti' => null]);
        DB::table('karyawans')->insert(['nama_lengkap' => 'BUDI LAIN', 'nama_panggilan' => 'BUDI', 'pekerjaan' => 'OPERATOR FORKLIFT']);
        $this->assertSame(422, $this->storeRows([$row])->getStatusCode());
        $this->assertDatabaseCount('langsir_batams', 0);
    }

    public function test_missing_tariff_requires_manual_cost(): void
    {
        DB::table('master_pricelist_lolo_batams')->delete();
        $row = array_replace($this->row('CONT-LOLO', 'PB', 'PB-01'), ['menggunakan_lolo' => 'Ya', 'operator_lolo_1' => 'BUDI', 'operator_lolo_2' => 'AGUS']);
        $this->assertSame(422, $this->storeRows([$row])->getStatusCode());
        $this->assertSame(200, $this->storeRows([array_replace($row, ['biaya_lolo' => '100.000'])])->getStatusCode());
        $this->assertDatabaseHas('langsir_batams', ['no_kontainer' => 'CONT-LOLO', 'biaya_lolo' => 100000]);
    }

    private function storeRows(array $rows)
    {
        return Model::withoutEvents(fn () => (new LangsirBatamController)->storeBulk(
            Request::create('/langsir-batam/store-bulk', 'POST', ['rows' => $rows])
        ));
    }
}
