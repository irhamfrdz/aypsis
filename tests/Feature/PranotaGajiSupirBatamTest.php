<?php

namespace Tests\Feature;

use App\Http\Controllers\PranotaGajiSupirBatamController;
use App\Models\PranotaGajiSupirBatam;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PranotaGajiSupirBatamTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        foreach (['karyawans', 'karyawan_tidak_tetaps'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('nik');
                $table->string('nama_lengkap');
            });
        }
        DB::table('karyawans')->insert([
            ['id' => 1, 'nik' => '001', 'nama_lengkap' => 'HADI PRAMONO'],
            ['id' => 2, 'nik' => '002', 'nama_lengkap' => 'SUPIR KEDUA'],
        ]);

        Schema::create('gaji_supir_batams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->decimal('total_gaji', 15, 2);
            $table->string('status_pembayaran');
            $table->timestamps();
        });

        (require database_path('migrations/2026_11_03_100000_create_pranota_gaji_supir_batam_tables.php'))->up();
        (require database_path('migrations/2026_11_05_100000_add_pph_to_pranota_gaji_supir_batam_items.php'))->up();
        (require database_path('migrations/2026_11_05_110000_add_bpjs_to_pranota_gaji_supir_batam_items.php'))->up();
        (require database_path('migrations/2026_08_28_112737_create_pranota_bpjs_headers_table.php'))->up();
        (require database_path('migrations/2026_08_28_112738_create_pranota_bpjs_details_table.php'))->up();
        Schema::table('pranota_bpjs_details', function (Blueprint $table) {
            $table->string('tipe_karyawan')->nullable();
            $table->json('snapshot_bpjs')->nullable();
            $table->json('rincian_bpjs')->nullable();
        });
    }

    public function test_creating_pranota_saves_selected_pending_salaries_and_prevents_reuse(): void
    {
        $first = DB::table('gaji_supir_batams')->insertGetId(['karyawan_id' => 1, 'total_gaji' => 100000, 'status_pembayaran' => 'PENDING']);
        $second = DB::table('gaji_supir_batams')->insertGetId(['karyawan_id' => 2, 'total_gaji' => 250000, 'status_pembayaran' => 'PENDING']);
        $paid = DB::table('gaji_supir_batams')->insertGetId(['karyawan_id' => 3, 'total_gaji' => 50000, 'status_pembayaran' => 'PAID']);
        $controller = new PranotaGajiSupirBatamController;

        $controller->store(Request::create('/pranota-gaji-supir-batam', 'POST', [
            'tanggal_pranota' => '2026-11-03', 'gaji_ids' => [$first, $second], 'pph' => [1 => 0, 2 => 0],
        ]));

        $pranota = PranotaGajiSupirBatam::with('items')->firstOrFail();
        $this->assertSame('PGSB/2026/00001', $pranota->nomor_pranota);
        $this->assertSame('350000.00', $pranota->total_gaji);
        $this->assertCount(2, $pranota->items);

        foreach ([$first, $paid] as $unavailable) {
            try {
                $controller->store(Request::create('/pranota-gaji-supir-batam', 'POST', [
                    'tanggal_pranota' => '2026-11-03', 'gaji_ids' => [$unavailable], 'pph' => [1 => 0],
                ]));
                $this->fail('Gaji yang tidak tersedia seharusnya ditolak.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('gaji_ids', $exception->errors());
            }
        }

        $this->assertSame(1, PranotaGajiSupirBatam::count());
    }

    public function test_latest_matching_bpjs_is_deducted_once_across_periods_and_frozen(): void
    {
        $this->bpjs('2026-10-01', 'HADI PRAMONO', 10000);
        $detailId = $this->bpjs('2026-11-01', 'HADI PRAMONO', 30000);
        $this->bpjs('2026-11-02', 'NAMA BERBEDA', 90000);
        $first = DB::table('gaji_supir_batams')->insertGetId(['karyawan_id' => 1, 'total_gaji' => 20000, 'status_pembayaran' => 'PENDING']);
        $second = DB::table('gaji_supir_batams')->insertGetId(['karyawan_id' => 1, 'total_gaji' => 80000, 'status_pembayaran' => 'PENDING']);
        (new PranotaGajiSupirBatamController)->store(Request::create('/', 'POST', [
            'tanggal_pranota' => '2026-11-05', 'gaji_ids' => [$first, $second], 'pph' => [1 => 5000],
        ]));
        $pranota = PranotaGajiSupirBatam::with('items')->firstOrFail();
        $this->assertSame('65000.00', $pranota->total_gaji);
        $this->assertEquals(30000, $pranota->items->sum('bpjs'));
        $this->assertEquals(5000, $pranota->items->sum('pph'));
        $this->assertEquals(65000, $pranota->items->sum('total_gaji'));
        $this->assertTrue($pranota->items->every(fn ($item) => (int) $item->bpjs_detail_id === $detailId));
        $this->bpjs('2026-11-06', 'HADI PRAMONO', 50000);
        $this->assertSame('65000.00', $pranota->fresh()->total_gaji);
    }

    public function test_bpjs_and_pph_cannot_exceed_selected_salary(): void
    {
        $this->bpjs('2026-11-01', 'HADI PRAMONO', 30000);
        $id = DB::table('gaji_supir_batams')->insertGetId(['karyawan_id' => 1, 'total_gaji' => 35000, 'status_pembayaran' => 'PENDING']);
        try {
            (new PranotaGajiSupirBatamController)->store(Request::create('/', 'POST', [
                'tanggal_pranota' => '2026-11-05', 'gaji_ids' => [$id], 'pph' => [1 => 10000],
            ]));
            $this->fail('Excess deductions must be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('pph.1', $exception->errors());
        }
        $this->assertSame(0, PranotaGajiSupirBatam::count());
    }

    private function bpjs(string $date, string $name, int $amount): int
    {
        $header = DB::table('pranota_bpjs_headers')->insertGetId([
            'nomor_pranota' => 'BPJS-'.$date, 'tanggal_pranota' => $date,
            'periode_bulan' => 11, 'periode_tahun' => 2026,
        ]);

        return DB::table('pranota_bpjs_details')->insertGetId([
            'pranota_bpjs_header_id' => $header, 'karyawan_id' => 1,
            'tipe_karyawan' => \App\Models\Karyawan::class,
            'snapshot_bpjs' => json_encode(['karyawan' => ['nik' => '001', 'nama_lengkap' => $name]]),
            'rincian_bpjs' => json_encode(['noncrew_jht_hutang' => $amount, 'jht_hutang' => 999999, 'noncrew_jht_biaya' => 999999]),
        ]);
    }
}
