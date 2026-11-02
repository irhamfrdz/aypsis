<?php

namespace Tests\Feature;

use App\Services\VehicleTireReplacementService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VehicleTireReplacementTest extends TestCase
{
    public function test_counts_used_tires_by_vehicle_and_installation_period_without_counting_each_tire_as_a_separate_proof(): void
    {
        foreach (['stock_bans', 'stock_ban_luar_batams'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('mobil_id')->nullable();
                $table->string('status');
                $table->date('tanggal_keluar')->nullable();
                $table->string('nomor_bukti_pakai')->nullable();
            });
        }

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

        $this->assertSame(['replacement_count' => 4, 'tire_count' => 6], $summary->get(1));
        $this->assertSame(['replacement_count' => 1, 'tire_count' => 1], $summary->get(2));
        $this->assertFalse($summary->has(3));
        $this->assertFalse($summary->has(4));
        $this->assertTrue($service->summarize([], '2026-10-01', '2026-10-31')->isEmpty());
    }
}
