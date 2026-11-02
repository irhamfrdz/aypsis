<?php

namespace Tests\Feature;

use App\Services\VehicleTireReplacementService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VehicleTireReplacementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        foreach (['stock_bans', 'stock_ban_luar_batams'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('mobil_id')->nullable();
                $table->string('status');
                $table->date('tanggal_keluar')->nullable();
                $table->date('tanggal_digunakan')->nullable();
                $table->string('nomor_bukti_pakai')->nullable();
                $table->unsignedBigInteger('penerima_id')->nullable();
                $table->string('penerima_manual')->nullable();
            });
        }
    }

    public function test_counts_each_used_tire_even_with_the_same_proof_and_filters_vehicle_and_period(): void
    {
        $base = ['mobil_id' => 1, 'status' => 'Terpakai', 'tanggal_keluar' => '2026-10-01', 'nomor_bukti_pakai' => 'P001'];
        DB::table('stock_bans')->insert([
            $base,
            $base,
            array_replace($base, ['nomor_bukti_pakai' => null]),
            array_replace($base, ['tanggal_keluar' => '2026-10-31', 'nomor_bukti_pakai' => '']),
            array_replace($base, ['tanggal_keluar' => '2026-09-30']),
            array_replace($base, ['tanggal_keluar' => '2026-11-01']),
            array_replace($base, ['tanggal_keluar' => null]),
            array_replace($base, ['status' => 'Stok']),
            array_replace($base, ['status' => 'Dijual']),
            array_replace($base, ['mobil_id' => 2]),
            array_replace($base, ['mobil_id' => 3]),
            array_replace($base, ['mobil_id' => null]),
        ]);
        DB::table('stock_ban_luar_batams')->insert([$base, $base]);

        $service = app(VehicleTireReplacementService::class);
        $summary = $service->summarize([1, 2, 4], '2026-10-01', '2026-10-31');

        $this->assertSame(['replacement_count' => 6, 'tire_count' => 6], $summary->get(1));
        $this->assertSame(['replacement_count' => 1, 'tire_count' => 1], $summary->get(2));
        $this->assertFalse($summary->has(3));
        $this->assertFalse($summary->has(4));
        $this->assertTrue($service->summarize([], '2026-10-01', '2026-10-31')->isEmpty());
    }

    public function test_beni_receives_two_tires_on_another_vehicle_with_the_same_proof(): void
    {
        // Vehicle 1 represents B9740UIW; vehicle 9 represents JKT2022409.
        $base = ['mobil_id' => 9, 'status' => 'Terpakai', 'tanggal_keluar' => '2026-09-30',
            'tanggal_digunakan' => '2026-10-30', 'nomor_bukti_pakai' => 'P261000330',
            'penerima_id' => 10, 'penerima_manual' => null];
        DB::table('stock_bans')->insert([$base, $base]);
        $drivers = [1 => ['id' => 10, 'names' => ['BENI', 'BENI SUPIR']]];
        $service = app(VehicleTireReplacementService::class);

        $this->assertSame(['replacement_count' => 2, 'tire_count' => 2],
            $service->summarize([1], '2026-10-01', '2026-10-31', $drivers)->get(1));
        $this->assertTrue($service->summarize([1], '2026-09-01', '2026-09-30', $drivers)->isEmpty());

        DB::table('stock_bans')->insert([
            array_replace($base, ['penerima_id' => null, 'penerima_manual' => ' beni ']),
            array_replace($base, ['mobil_id' => 1, 'penerima_id' => 20]), // Another driver on this vehicle.
            array_replace($base, ['mobil_id' => 1, 'penerima_id' => null]), // Legacy record without a receiver.
            array_replace($base, ['status' => 'Dikembalikan']),
            array_replace($base, ['tanggal_digunakan' => '2026-11-01']),
            array_replace($base, ['mobil_id' => null]),
        ]);
        DB::table('stock_ban_luar_batams')->insert([
            'mobil_id' => 9, 'status' => 'Terpakai', 'tanggal_keluar' => '2026-10-31', 'penerima_id' => 10,
        ]);
        $summary = $service->summarize([1, 2], '2026-10-01', '2026-10-31', $drivers);
        $this->assertSame(['replacement_count' => 5, 'tire_count' => 5], $summary->get(1));
        $this->assertFalse($summary->has(2));
    }
}
