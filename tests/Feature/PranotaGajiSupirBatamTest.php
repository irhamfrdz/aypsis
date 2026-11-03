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
    }

    public function test_creating_pranota_saves_selected_pending_salaries_and_prevents_reuse(): void
    {
        $first = DB::table('gaji_supir_batams')->insertGetId(['karyawan_id' => 1, 'total_gaji' => 100000, 'status_pembayaran' => 'PENDING']);
        $second = DB::table('gaji_supir_batams')->insertGetId(['karyawan_id' => 2, 'total_gaji' => 250000, 'status_pembayaran' => 'PENDING']);
        $paid = DB::table('gaji_supir_batams')->insertGetId(['karyawan_id' => 3, 'total_gaji' => 50000, 'status_pembayaran' => 'PAID']);
        $controller = new PranotaGajiSupirBatamController;

        $controller->store(Request::create('/pranota-gaji-supir-batam', 'POST', [
            'tanggal_pranota' => '2026-11-03', 'gaji_ids' => [$first, $second],
        ]));

        $pranota = PranotaGajiSupirBatam::with('items')->firstOrFail();
        $this->assertSame('PGSB/2026/00001', $pranota->nomor_pranota);
        $this->assertSame('350000.00', $pranota->total_gaji);
        $this->assertCount(2, $pranota->items);

        foreach ([$first, $paid] as $unavailable) {
            try {
                $controller->store(Request::create('/pranota-gaji-supir-batam', 'POST', [
                    'tanggal_pranota' => '2026-11-03', 'gaji_ids' => [$unavailable],
                ]));
                $this->fail('Gaji yang tidak tersedia seharusnya ditolak.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('gaji_ids', $exception->errors());
            }
        }

        $this->assertSame(1, PranotaGajiSupirBatam::count());
    }
}
