<?php

namespace Tests\Feature;

use App\Http\Controllers\GerakVoyageController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GerakVoyageDashboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kapal')->nullable();
            $table->string('no_voyage')->nullable();
            $table->date('tanggal_berangkat')->nullable();
            $table->date('tanggal_muat')->nullable();
            $table->date('tanggal_mulai_berlayar')->nullable();
            $table->date('tanggal_berlabuh')->nullable();
            $table->date('tanggal_sandar')->nullable();
            $table->date('tanggal_mulai_bongkar')->nullable();
            $table->date('tanggal_selesai_bongkar')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('manifests');

        parent::tearDown();
    }

    public function test_dashboard_shows_only_latest_voyage_for_each_ship(): void
    {
        DB::table('manifests')->insert([
            ['nama_kapal' => 'Kapal A', 'no_voyage' => 'V-02', 'tanggal_berangkat' => '2026-09-24', 'tanggal_muat' => null, 'created_at' => '2026-09-23 10:00:00'],
            ['nama_kapal' => 'Kapal A', 'no_voyage' => 'V-02', 'tanggal_berangkat' => '2026-09-24', 'tanggal_muat' => null, 'created_at' => '2026-09-23 11:00:00'],
            ['nama_kapal' => 'Kapal B', 'no_voyage' => 'V-03', 'tanggal_berangkat' => '2026-09-22', 'tanggal_muat' => '2026-09-21', 'created_at' => '2026-09-20 10:00:00'],
            ['nama_kapal' => 'Kapal A', 'no_voyage' => 'V-01', 'tanggal_berangkat' => '2026-09-20', 'tanggal_muat' => '2026-09-19', 'created_at' => '2026-09-18 10:00:00'],
        ]);

        $controller = new GerakVoyageController;
        $data = $controller->dashboard(Request::create('/gerak-voyage/dashboard'))->getData();

        $this->assertSame(2, $data['totalShips']);
        $this->assertSame(1, $data['shipsWithDates']);
        $this->assertSame(1, $data['shipsWithoutDates']);
        $this->assertSame(2, $data['voyages']->total());
        $this->assertSame('V-02', $data['voyages']->first()->no_voyage);
        $this->assertSame(2, (int) $data['voyages']->first()->jumlah_manifest);

        $filtered = $controller->dashboard(Request::create('/gerak-voyage/dashboard', 'GET', [
            'status' => 'belum_terisi',
        ]))->getData();

        $this->assertSame(1, $filtered['voyages']->total());
        $this->assertSame('V-02', $filtered['voyages']->first()->no_voyage);

        $olderVoyageSearch = $controller->dashboard(Request::create('/gerak-voyage/dashboard', 'GET', [
            'no_voyage' => 'V-01',
        ]))->getData();

        $this->assertSame(0, $olderVoyageSearch['voyages']->total());
    }

    public function test_latest_voyage_uses_manifest_creation_date_when_schedule_is_empty(): void
    {
        DB::table('manifests')->insert([
            ['nama_kapal' => 'Kapal C', 'no_voyage' => 'V-07', 'created_at' => '2026-09-20 10:00:00'],
            ['nama_kapal' => 'Kapal C', 'no_voyage' => 'V-08', 'created_at' => '2026-09-25 10:00:00'],
        ]);

        $data = (new GerakVoyageController)->dashboard(Request::create('/gerak-voyage/dashboard'))->getData();

        $this->assertSame('V-08', $data['voyages']->first()->no_voyage);
        $this->assertSame(1, $data['shipsWithoutDates']);
    }
}
