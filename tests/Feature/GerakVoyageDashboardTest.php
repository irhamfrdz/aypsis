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
            $table->date('tanggal_muat')->nullable();
            $table->date('tanggal_mulai_berlayar')->nullable();
            $table->date('tanggal_berlabuh')->nullable();
            $table->date('tanggal_sandar')->nullable();
            $table->date('tanggal_mulai_bongkar')->nullable();
            $table->date('tanggal_selesai_bongkar')->nullable();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('manifests');

        parent::tearDown();
    }

    public function test_dashboard_groups_manifests_by_voyage_and_filters_missing_dates(): void
    {
        DB::table('manifests')->insert([
            ['nama_kapal' => 'Kapal A', 'no_voyage' => 'V-01', 'tanggal_muat' => '2026-09-20'],
            ['nama_kapal' => 'Kapal A', 'no_voyage' => 'V-01', 'tanggal_muat' => '2026-09-20'],
            ['nama_kapal' => 'Kapal A', 'no_voyage' => 'V-02', 'tanggal_muat' => null],
        ]);

        $controller = new GerakVoyageController;
        $data = $controller->dashboard(Request::create('/gerak-voyage/dashboard'))->getData();

        $this->assertSame(2, $data['totalVoyages']);
        $this->assertSame(1, $data['voyagesWithDates']);
        $this->assertSame(1, $data['voyagesWithoutDates']);
        $this->assertSame(2, $data['voyages']->total());
        $this->assertSame(2, (int) $data['voyages']->first()->jumlah_manifest);

        $filtered = $controller->dashboard(Request::create('/gerak-voyage/dashboard', 'GET', [
            'status' => 'belum_terisi',
        ]))->getData();

        $this->assertSame(1, $filtered['voyages']->total());
        $this->assertSame('V-02', $filtered['voyages']->first()->no_voyage);
    }
}
