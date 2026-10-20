<?php

namespace Tests\Feature;

use App\Http\Controllers\ArmadaAvailabilityController;
use App\Http\Controllers\DashboardArmadaController;
use App\Http\Controllers\MasterArmadaController;
use App\Models\ArmadaUnavailability;
use App\Models\Karyawan;
use App\Models\Mobil;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MasterArmadaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-10-20 12:00:00');
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nik');
        });
        Schema::create('mobils', function (Blueprint $table) {
            $table->id();
            foreach (['kode_no', 'nomor_polisi', 'nickname', 'merek', 'jenis', 'lokasi', 'pemakai', 'imei_gps'] as $column) {
                $table->string($column)->nullable();
            }
            $table->integer('tahun_pembuatan')->nullable();
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->timestamps();
        });
        (require database_path('migrations/2026_10_19_100000_add_km_to_mobils_table.php'))->up();
        Schema::create('users', fn (Blueprint $table) => $table->id());
        (require database_path('migrations/2026_10_19_110000_create_armada_unavailabilities_table.php'))->up();
        Schema::create('gps_daily_distances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mobil_id');
            $table->date('date');
            $table->decimal('distance_km', 12, 3);
            $table->unsignedInteger('point_count')->default(1);
        });
        foreach ([
            ['surat_jalans', 'tanda_terimas', 'surat_jalan_id', 'tanggal'],
            ['surat_jalan_bongkarans', 'tanda_terima_bongkarans', 'surat_jalan_bongkaran_id', 'tanggal_tanda_terima'],
            ['surat_jalan_batams', 'tanda_terima_batams', 'surat_jalan_batam_id', 'tanggal'],
            ['surat_jalan_bongkaran_batams', 'tanda_terima_bongkaran_batams', 'surat_jalan_bongkaran_id', 'tanggal_tanda_terima'],
            ['surat_jalan_tarik_kosong_batams', 'tanda_terima_surat_jalan_tarik_kosong_batams', 'surat_jalan_tarik_kosong_batam_id', 'tanggal_tanda_terima'],
        ] as [$source, $receipt, $foreignKey, $dateColumn]) {
            Schema::create($source, function (Blueprint $table) {
                $table->id();
                $table->string('no_plat');
                $table->string('supir')->nullable();
                $table->string('status')->nullable();
                $table->string('rit')->nullable();
            });
            Schema::create($receipt, function (Blueprint $table) use ($foreignKey, $dateColumn) {
                $table->id();
                $table->unsignedBigInteger($foreignKey);
                $table->date($dateColumn);
                $table->softDeletes();
            });
        }
        DB::table('karyawans')->insert(['id' => 1, 'nama_lengkap' => 'SUPIR ALEXINDO', 'nik' => '001']);
        DB::table('mobils')->insert([
            ['id' => 1, 'nomor_polisi' => 'B 1001 AA', 'kode_no' => 'JKT-01', 'lokasi' => 'JKT', 'karyawan_id' => 1],
            ['id' => 2, 'nomor_polisi' => 'BP 1002 AA', 'kode_no' => 'BTM-02', 'lokasi' => 'BTM', 'karyawan_id' => null],
            ['id' => 3, 'nomor_polisi' => 'BP 1003 AA', 'kode_no' => 'BTM-03', 'lokasi' => 'BTM', 'karyawan_id' => null],
        ]);
        DB::table('mobils')->update(['imei_gps' => '123456789012345']);
        DB::table('mobils')->update(['jenis' => 'HEAD']);
        DB::table('mobils')->where('id', 1)->update(['km' => 777.125]);
        DB::table('mobils')->where('id', 2)->update(['km' => 0]);
        foreach ([null, '', '   '] as $index => $imei) {
            DB::table('mobils')->insert([
                'nomor_polisi' => 'B NON GPS '.$index, 'kode_no' => 'TANPA-GPS-'.$index,
                'lokasi' => 'BTM', 'imei_gps' => $imei,
            ]);
        }
        DB::table('gps_daily_distances')->insert([
            ['mobil_id' => 1, 'date' => '2026-10-18', 'distance_km' => 1234.125],
            ['mobil_id' => 1, 'date' => '2026-10-19', 'distance_km' => 25.5],
            ['mobil_id' => 2, 'date' => '2026-10-19', 'distance_km' => 0],
        ]);
    }

    private function indexData(array $filters = [], ?string $cabang = null): array
    {
        $request = Request::create('/master/armada', 'GET', $filters);
        if ($cabang !== null) {
            $user = (new User)->setRelation('karyawan', new Karyawan(['cabang' => $cabang]));
            $request->setUserResolver(fn () => $user);
        }

        return (new MasterArmadaController)->index($request)->getData();
    }

    public function test_lists_existing_mobils_and_searches_vehicle_and_driver(): void
    {
        $this->assertSame(3, $this->indexData()['armadas']->total());
        $this->assertSame([2], $this->indexData(['search' => 'BTM-02'])['armadas']->pluck('id')->all());
        $this->assertSame([1], $this->indexData(['search' => 'SUPIR ALEXINDO'])['armadas']->pluck('id')->all());
        $this->assertSame([1], $this->indexData(['search' => 'B 1001'])['armadas']->pluck('id')->all());
        $this->assertSame(0, $this->indexData(['search' => 'TIDAK ADA'])['armadas']->total());
        $this->assertSame(0, $this->indexData(['search' => 'TANPA-GPS'])['armadas']->total());
    }

    public function test_batam_user_only_sees_batam_vehicles_even_when_searching(): void
    {
        $this->assertSame([2, 3], $this->indexData([], 'BTM')['armadas']->pluck('id')->all());
        $this->assertSame(0, $this->indexData(['search' => 'SUPIR ALEXINDO'], 'BTM')['armadas']->total());
        $this->assertSame([2], $this->indexData(['search' => 'BTM-02'], 'BTM')['armadas']->pluck('id')->all());
    }

    public function test_km_uses_manual_value_and_preserves_zero_and_missing_data(): void
    {
        $armadas = $this->indexData()['armadas']->keyBy('id');
        $this->assertEquals('777.125', $armadas[1]->km);
        $this->assertNotNull($armadas[2]->km);
        $this->assertEquals(0, $armadas[2]->km);
        $this->assertNull($armadas[3]->km);
    }

    public function test_route_uses_existing_vehicle_permission_and_authentication(): void
    {
        $route = Route::getRoutes()->getByName('master.armada.index');
        $this->assertNotNull($route);
        $this->assertContains('auth', $route->gatherMiddleware());
        $this->assertContains('can:master-mobil-view', $route->gatherMiddleware());
        $updateRoute = Route::getRoutes()->getByName('master.armada.update-km');
        $this->assertContains('can:master-mobil-update', $updateRoute->gatherMiddleware());
    }

    public function test_page_renders_existing_vehicle_and_driver_with_detail_link(): void
    {
        $template = str_replace(
            ["@extends('layouts.app')", "@section('content')", '@endsection'], '',
            file_get_contents(resource_path('views/master-armada/index.blade.php'))
        );
        $html = Blade::render($template, $this->indexData() + ['errors' => new ViewErrorBag]);
        $this->assertStringContainsString('Master Armada', $html);
        $this->assertStringContainsString('B 1001 AA', $html);
        $this->assertStringContainsString('SUPIR ALEXINDO', $html);
        $this->assertStringContainsString('777,125', $html);
        $this->assertStringContainsString('0,000', $html);
        $this->assertStringContainsString(route('master.mobil.show', 1), $html);
    }

    public function test_manual_km_can_be_saved_and_cleared_without_changing_gps_data(): void
    {
        $controller = new MasterArmadaController;
        $armada = Mobil::findOrFail(1);
        Mobil::withoutEvents(fn () => $controller->updateKm(Request::create('/km', 'PATCH', ['km' => '54321.125']), $armada));
        $this->assertSame('54321.125', $armada->fresh()->km);
        $this->assertEquals(1234.125, DB::table('gps_daily_distances')->where('id', 1)->value('distance_km'));

        Mobil::withoutEvents(fn () => $controller->updateKm(Request::create('/km', 'PATCH', ['km' => null]), $armada));
        $this->assertNull($armada->fresh()->km);
    }

    public function test_invalid_km_is_rejected_without_changing_saved_value(): void
    {
        foreach (['-1', 'abc', '1.1234', '1000000000'] as $km) {
            try {
                (new MasterArmadaController)->updateKm(Request::create('/km', 'PATCH', ['km' => $km]), Mobil::findOrFail(1));
                $this->fail('Invalid Km must not be saved.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('km', $exception->errors());
            }
        }
        $this->assertSame('777.125', Mobil::findOrFail(1)->km);
    }

    public function test_batam_user_cannot_update_a_jakarta_vehicle(): void
    {
        $request = Request::create('/km', 'PATCH', ['km' => '100']);
        $request->setUserResolver(fn () => (new User)->setRelation('karyawan', new Karyawan(['cabang' => 'BTM'])));
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new MasterArmadaController)->updateKm($request, Mobil::findOrFail(1));
    }

    public function test_authorized_user_can_see_manual_km_form(): void
    {
        $this->be((new User)->forceFill(['id' => 1, 'username' => 'kiky']));
        $template = str_replace(
            ["@extends('layouts.app')", "@section('content')", '@endsection'], '',
            file_get_contents(resource_path('views/master-armada/index.blade.php'))
        );
        $html = Blade::render($template, $this->indexData() + ['errors' => new ViewErrorBag]);
        $this->assertStringContainsString(route('master.armada.update-km', 1), $html);
        $this->assertStringContainsString('value="777.125"', $html);
        $this->assertStringContainsString('Simpan', $html);
    }

    private function dashboardData(array $filters = [], ?string $cabang = null): array
    {
        $request = Request::create('/dashboard-armada', 'GET', $filters);
        if ($cabang !== null) {
            $request->setUserResolver(fn () => (new User)->setRelation('karyawan', new Karyawan(['cabang' => $cabang])));
        }

        return (new DashboardArmadaController)->index($request)->getData();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function gpsDays(int $mobilId, int $days = 7, string $end = '2026-10-19'): void
    {
        $start = Carbon::parse($end)->subDays($days - 1);
        for ($day = 0; $day < $days; $day++) {
            DB::table('gps_daily_distances')->updateOrInsert(
                ['mobil_id' => $mobilId, 'date' => $start->copy()->addDays($day)->toDateString()],
                ['distance_km' => 0, 'point_count' => 10]
            );
        }
    }

    private function trip(string $plate, string $date, int $count = 1, string $driver = 'SUPIR TRANSAKSI'): void
    {
        for ($number = 0; $number < $count; $number++) {
            $id = DB::table('surat_jalans')->insertGetId(['no_plat' => $plate, 'supir' => $driver, 'rit' => 'menggunakan_rit']);
            DB::table('tanda_terimas')->insert(['surat_jalan_id' => $id, 'tanggal' => $date]);
        }
    }

    private function rankingRow(int $id, array $filters = []): array
    {
        return $this->dashboardData($filters)['rankings']->getCollection()->first(fn ($row) => $row['mobil']->id === $id);
    }

    public function test_default_period_is_seven_complete_days_and_thirty_days_is_supported(): void
    {
        $data = $this->dashboardData();
        $this->assertSame('2026-10-19', $data['tanggalRanking']->toDateString());
        $this->assertSame('2026-10-13', $data['tanggalMulai']->toDateString());
        $this->assertSame(7, $data['periode']);
        $data = $this->dashboardData(['periode' => 30]);
        $this->assertSame('2026-09-20', $data['tanggalMulai']->toDateString());
        $this->assertSame(30, $data['rankings']->first()['available_days']);
        $this->expectException(ValidationException::class);
        $this->dashboardData(['periode' => 14]);
    }

    public function test_custom_date_range_includes_both_boundaries_and_filters_activity_and_availability(): void
    {
        $this->trip('B 1001 AA', '2026-10-16', 4);
        $this->trip('B 1001 AA', '2026-10-17');
        $this->trip('B 1001 AA', '2026-10-18', 2);
        $this->trip('B 1001 AA', '2026-10-19', 8);
        foreach (['2026-10-16', '2026-10-17', '2026-10-18', '2026-10-19'] as $date) {
            ArmadaUnavailability::withoutEvents(fn () => ArmadaUnavailability::create([
                'mobil_id' => 2, 'start_date' => $date, 'end_date' => $date, 'reason' => 'Servis',
            ]));
        }

        $filters = ['tanggal_mulai' => '2026-10-17', 'tanggal_selesai' => '2026-10-18'];
        $data = $this->dashboardData($filters);
        $this->assertSame(2, $data['periode']);
        $row = $data['rankings']->first(fn ($row) => $row['mobil']->id === 1);
        $this->assertSame(3, $row['rit']);
        $this->assertSame(['2026-10-17', '2026-10-18'], $row['daily']->pluck('date')->all());
        $this->assertEquals(1234.125, $row['gps_km']);
        $this->assertSame(2, $data['availabilityPeriods']->total());

        $singleDay = $this->dashboardData(['tanggal_mulai' => '2026-10-18', 'tanggal_selesai' => '2026-10-18']);
        $this->assertSame(1, $singleDay['periode']);
        $this->assertSame(2, $singleDay['rankingSummary']['rit']);
    }

    public function test_custom_date_range_rejects_reversed_future_invalid_and_incomplete_dates(): void
    {
        foreach ([
            ['tanggal_mulai' => '2026-10-19', 'tanggal_selesai' => '2026-10-18'],
            ['tanggal_mulai' => '2026-10-19', 'tanggal_selesai' => '2026-10-21'],
            ['tanggal_mulai' => 'invalid', 'tanggal_selesai' => '2026-10-19'],
            ['tanggal_mulai' => '2026-10-18'],
            ['tanggal_selesai' => '2026-10-19'],
        ] as $filters) {
            try {
                $this->dashboardData($filters);
                $this->fail('Invalid date range was accepted.');
            } catch (ValidationException $exception) {
                $this->assertNotEmpty($exception->errors());
            }
        }
    }

    public function test_dashboard_respects_gps_branch_search_location_and_vehicle_type(): void
    {
        $data = $this->dashboardData([], 'BTM');
        $this->assertSame(2, $data['totalArmada']);
        $this->assertSame(['BTM'], $data['lokasis']->all());
        $this->assertSame([2, 3], $data['fleetOptions']->pluck('id')->all());
        $this->assertSame(0, $this->dashboardData(['search' => 'SUPIR ALEXINDO'], 'BTM')['totalArmada']);
        $this->assertSame(0, $this->dashboardData(['lokasi' => 'JKT'], 'BTM')['totalArmada']);
        $this->assertSame(0, $this->dashboardData(['jenis' => 'PICKUP'])['totalArmada']);
        $this->assertSame(3, $this->dashboardData(['jenis' => 'HEAD'])['totalArmada']);
    }

    public function test_utilization_classifies_available_days_and_does_not_use_initial_km(): void
    {
        $this->gpsDays(1);
        $this->trip('B 1001 AA', '2026-10-13', 2);
        $this->trip('B 1001 AA', '2026-10-14');
        DB::table('gps_daily_distances')->where('mobil_id', 1)->where('date', '2026-10-14')->update(['distance_km' => 30]);
        DB::table('gps_daily_distances')->where('mobil_id', 1)->where('date', '2026-10-15')->update(['distance_km' => 50]);
        $row = $this->rankingRow(1);
        $this->assertSame(7, $row['available_days']);
        $this->assertSame(2, $row['productive_days']);
        $this->assertSame(3, $row['rit']);
        $this->assertSame(1, $row['moving_without_rit_days']);
        $this->assertSame(4, $row['idle_days']);
        $this->assertSame(0, $row['unknown_days']);
        $this->assertEqualsWithDelta(28.5714, $row['utilization'], 0.0001);
        $this->assertEqualsWithDelta(3 / 7, $row['average_rit'], 0.0001);
        $this->assertEquals(80, $row['gps_km']);
        $this->assertSame(1, $row['rank']);
        DB::table('mobils')->where('id', 1)->update(['km' => null]);
        $this->assertEquals($row['utilization'], $this->rankingRow(1)['utilization']);
        $this->assertSame(1, $this->rankingRow(1)['rank']);
    }

    public function test_missing_gps_stays_unknown_and_produces_a_labelled_provisional_rank(): void
    {
        $this->gpsDays(1);
        $this->trip('B 1001 AA', '2026-10-13');
        DB::table('gps_daily_distances')->where('mobil_id', 1)->where('date', '2026-10-14')->delete();
        $row = $this->rankingRow(1);
        $this->assertSame(7, $row['available_days']);
        $this->assertSame(1, $row['unknown_days']);
        $this->assertSame(5, $row['idle_days']);
        $this->assertSame(1, $row['gps_missing_days']);
        $this->assertEqualsWithDelta(100 / 7, $row['utilization'], 0.0001);
        $this->assertSame(1, $row['rank']);
        $this->assertTrue($row['rank_is_provisional']);
        $this->assertStringContainsString('GPS belum lengkap', $row['data_note']);
    }

    public function test_zero_points_or_negative_distance_are_missing_data_and_not_idle(): void
    {
        $this->gpsDays(1);
        DB::table('gps_daily_distances')->where('mobil_id', 1)->where('date', '2026-10-13')->update(['point_count' => 0]);
        DB::table('gps_daily_distances')->where('mobil_id', 1)->where('date', '2026-10-14')->update(['distance_km' => -1]);
        $row = $this->rankingRow(1);
        $this->assertSame(2, $row['unknown_days']);
        $this->assertSame(5, $row['idle_days']);
        $this->assertSame(1, $row['rank']);
        $this->assertTrue($row['rank_is_provisional']);
    }

    public function test_provisional_status_applies_to_all_ranked_vehicles_in_the_filtered_result(): void
    {
        $this->gpsDays(1);
        $this->gpsDays(2);
        $this->gpsDays(3);
        DB::table('gps_daily_distances')->where('mobil_id', 3)->where('date', '2026-10-13')->delete();
        $this->trip('BP 1002 AA', '2026-10-14');
        $this->trip('BP 1003 AA', '2026-10-15');
        $this->assertTrue($this->rankingRow(2)['rank_is_provisional']);
        $this->assertTrue($this->rankingRow(3)['rank_is_provisional']);
        $this->gpsDays(3);
        $this->assertFalse($this->rankingRow(2)['rank_is_provisional']);
        $this->assertFalse($this->rankingRow(3)['rank_is_provisional']);
    }

    public function test_vehicle_with_no_gps_or_rit_in_the_period_remains_unranked(): void
    {
        $row = $this->rankingRow(3);
        $this->assertNull($row['rank']);
        $this->assertFalse($row['rank_is_provisional']);
        $this->assertSame('Belum ada data aktivitas pada periode ini.', $row['unranked_reason']);
    }

    public function test_overlapping_and_ongoing_unavailability_is_excluded_once_per_day(): void
    {
        $this->gpsDays(1);
        $this->trip('B 1001 AA', '2026-10-16');
        DB::table('armada_unavailabilities')->insert([
            ['mobil_id' => 1, 'start_date' => '2026-10-13', 'end_date' => '2026-10-14', 'reason' => 'Servis'],
            ['mobil_id' => 1, 'start_date' => '2026-10-14', 'end_date' => '2026-10-15', 'reason' => 'Perbaikan'],
        ]);
        $row = $this->rankingRow(1);
        $this->assertSame(3, $row['unavailable_days']);
        $this->assertSame(4, $row['available_days']);
        $this->assertEquals(25, $row['utilization']);
        DB::table('armada_unavailabilities')->insert(['mobil_id' => 1, 'start_date' => '2026-10-18', 'reason' => 'Masih servis']);
        $row = $this->rankingRow(1);
        $this->assertSame(5, $row['unavailable_days']);
        $this->assertSame(2, $row['available_days']);
        $this->assertEquals(50, $row['utilization']);
    }

    public function test_full_unavailability_and_conflicting_activity_do_not_produce_a_rank(): void
    {
        $this->gpsDays(1);
        DB::table('armada_unavailabilities')->insert(['mobil_id' => 1, 'start_date' => '2026-10-01', 'reason' => 'Servis']);
        $row = $this->rankingRow(1);
        $this->assertSame(0, $row['available_days']);
        $this->assertNull($row['utilization']);
        $this->assertNull($row['average_rit']);
        $this->assertNull($row['rank']);
        DB::table('armada_unavailabilities')->update(['start_date' => '2026-10-13', 'end_date' => '2026-10-13']);
        $this->trip('B 1001 AA', '2026-10-13');
        $row = $this->rankingRow(1);
        $this->assertSame(1, $row['conflict_days']);
        $this->assertSame(0, $row['rit']);
        $this->assertNull($row['rank']);
        $this->assertStringContainsString('hari tidak tersedia', $row['unranked_reason']);
    }

    public function test_utilization_ranks_globally_across_location_and_type_before_rit_volume(): void
    {
        foreach ([1, 2, 3] as $id) {
            $this->gpsDays($id);
        }
        DB::table('mobils')->where('id', 2)->update(['lokasi' => 'JKT']);
        $this->trip('B 1001 AA', '2026-10-13');
        $this->trip('B 1001 AA', '2026-10-14');
        $this->trip('BP 1002 AA', '2026-10-13', 5);
        $this->trip('BP 1003 AA', '2026-10-13');
        $this->assertSame(1, $this->rankingRow(1)['rank']);
        $this->assertSame(2, $this->rankingRow(2)['rank']);
        $this->assertSame(3, $this->rankingRow(3)['rank']);
        DB::table('mobils')->where('id', 2)->update(['jenis' => 'PICKUP']);
        $this->assertSame(2, $this->rankingRow(2)['rank']);
        $ranks = $this->dashboardData()['rankings']->pluck('rank')->filter()->values()->all();
        $this->assertSame([1, 2, 3], $ranks);
        $this->assertSame(count($ranks), count(array_unique($ranks)));
    }

    public function test_completed_trips_are_deduplicated_across_receipts_and_sources(): void
    {
        $this->gpsDays(1);
        $this->trip('b-1001 aa', '2026-10-13', 1, 'SUPIR PENGGANTI');
        DB::table('tanda_terimas')->insert([
            ['surat_jalan_id' => 1, 'tanggal' => '2026-10-13'],
            ['surat_jalan_id' => 1, 'tanggal' => '2026-10-14'],
        ]);
        DB::table('surat_jalan_bongkarans')->insert(['id' => 1, 'no_plat' => 'B 1001 AA', 'supir' => 'SUPIR PENGGANTI']);
        DB::table('tanda_terima_bongkarans')->insert(['surat_jalan_bongkaran_id' => 1, 'tanggal_tanda_terima' => '2026-10-14']);
        $this->trip('B 1001 AA', '2026-10-12');
        DB::table('tanda_terimas')->insert(['surat_jalan_id' => 2, 'tanggal' => '2026-10-15']);
        $this->trip('B 1001 AA', '2026-10-15');
        DB::table('surat_jalans')->where('id', 3)->update(['status' => 'cancelled']);
        $this->trip('B 1001 AA', '2026-10-15');
        DB::table('surat_jalans')->where('id', 4)->update(['rit' => 'tidak_menggunakan_rit']);
        DB::table('surat_jalans')->insert(['no_plat' => 'B 1001 AA', 'status' => 'approved']);
        $row = $this->rankingRow(1);
        $this->assertSame(2, $row['rit']);
        $this->assertSame(2, $row['productive_days']);
        $this->assertSame(['SUPIR PENGGANTI' => 2], $row['rit_per_supir']->all());
    }

    public function test_batam_trip_types_and_soft_deleted_receipts_are_handled(): void
    {
        $this->gpsDays(2);
        foreach ([
            ['surat_jalan_batams', 'tanda_terima_batams', 'surat_jalan_batam_id', 'tanggal'],
            ['surat_jalan_bongkaran_batams', 'tanda_terima_bongkaran_batams', 'surat_jalan_bongkaran_id', 'tanggal_tanda_terima'],
            ['surat_jalan_tarik_kosong_batams', 'tanda_terima_surat_jalan_tarik_kosong_batams', 'surat_jalan_tarik_kosong_batam_id', 'tanggal_tanda_terima'],
        ] as [$source, $receipt, $key, $dateColumn]) {
            DB::table($source)->insert(['id' => 1, 'no_plat' => 'BP 1002 AA', 'supir' => 'SUPIR BTM']);
            DB::table($receipt)->insert([$key => 1, $dateColumn => '2026-10-19']);
        }
        DB::table('surat_jalan_batams')->insert(['id' => 2, 'no_plat' => 'BP 1002 AA']);
        DB::table('tanda_terima_batams')->insert(['surat_jalan_batam_id' => 2, 'tanggal' => '2026-10-19', 'deleted_at' => '2026-10-19 10:00:00']);
        $row = $this->rankingRow(2);
        $this->assertSame(3, $row['rit']);
        $this->assertSame(1, $row['productive_days']);
    }

    public function test_duplicate_plates_and_missing_data_do_not_get_a_rank(): void
    {
        $this->gpsDays(1);
        DB::table('mobils')->where('id', 3)->update(['nomor_polisi' => 'B1001AA']);
        $this->trip('B 1001 AA', '2026-10-13');
        $row = $this->rankingRow(1, ['lokasi' => 'JKT']);
        $this->assertFalse($row['plate_valid']);
        $this->assertNull($row['rit']);
        $this->assertNull($row['utilization']);
        $this->assertNull($row['rank']);
    }

    public function test_period_rank_is_global_before_pagination_and_stable_for_ties(): void
    {
        for ($number = 1; $number <= 18; $number++) {
            $id = DB::table('mobils')->insertGetId(['nomor_polisi' => 'B RANK '.$number, 'imei_gps' => 'GPS-'.$number, 'lokasi' => 'JKT', 'jenis' => 'HEAD']);
            $this->gpsDays($id);
            $this->trip('B RANK '.$number, '2026-10-13', $number);
        }
        $data = $this->dashboardData(['ranking_page' => 2, 'search' => 'B RANK']);
        $this->assertSame(18, $data['rankings']->total());
        $this->assertSame(16, $data['rankings']->first()['rank']);
        $this->assertSame(3, $data['rankings']->first()['rit']);
        $this->assertSame(171, $data['rankingSummary']['rit']);
    }

    public function test_availability_can_be_created_closed_and_deleted_with_validation(): void
    {
        $controller = new ArmadaAvailabilityController;
        ArmadaUnavailability::withoutEvents(fn () => $controller->store(Request::create('/availability', 'POST', [
            'mobil_id' => 1, 'start_date' => '2026-10-13', 'end_date' => null, 'reason' => 'Servis',
        ])));
        $period = ArmadaUnavailability::firstOrFail();
        $this->assertNull($period->end_date);
        ArmadaUnavailability::withoutEvents(fn () => $controller->update(Request::create('/availability', 'PATCH', [
            'start_date' => '2026-10-13', 'end_date' => '2026-10-14', 'reason' => 'Servis selesai',
        ]), $period));
        $this->assertSame('2026-10-14', $period->fresh()->end_date->toDateString());
        ArmadaUnavailability::withoutEvents(fn () => $controller->destroy(Request::create('/availability', 'DELETE'), $period));
        $this->assertSame(0, ArmadaUnavailability::count());
        $this->expectException(ValidationException::class);
        $controller->store(Request::create('/availability', 'POST', [
            'mobil_id' => 1, 'start_date' => '2026-10-14', 'end_date' => '2026-10-13', 'reason' => 'Servis',
        ]));
    }

    public function test_batam_user_cannot_change_other_branch_availability(): void
    {
        $request = Request::create('/availability', 'POST', ['mobil_id' => 1, 'start_date' => '2026-10-13', 'reason' => 'Servis']);
        $request->setUserResolver(fn () => (new User)->setRelation('karyawan', new Karyawan(['cabang' => 'BTM'])));
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new ArmadaAvailabilityController)->store($request);
    }

    public function test_dashboard_renders_empty_and_nonempty_period_data_and_checks_permissions(): void
    {
        $template = str_replace(["@extends('layouts.app')", "@section('content')", '@endsection'], '', file_get_contents(resource_path('views/dashboard-armada/index.blade.php')));
        $html = Blade::render($template, $this->dashboardData(['search' => 'TIDAK ADA']) + ['errors' => new ViewErrorBag]);
        $this->assertStringContainsString('Tidak ada armada yang sesuai', $html);
        $this->gpsDays(1);
        $this->trip('B 1001 AA', '2026-10-13');
        $this->be((new User)->forceFill(['id' => 1, 'username' => 'kiky']));
        DB::table('armada_unavailabilities')->insert(['mobil_id' => 1, 'start_date' => '2026-10-14', 'end_date' => '2026-10-14', 'reason' => 'Servis']);
        $html = Blade::render($template, $this->dashboardData() + ['errors' => new ViewErrorBag]);
        $this->assertStringContainsString('Ranking Utilisasi Armada', $html);
        $this->assertStringContainsString('Lihat rincian harian', $html);
        $this->assertStringContainsString('16,67%', $html);
        $this->assertStringNotContainsString('Skor / 100', $html);
        $this->assertStringNotContainsString('Km Awal Manual', $html);
        $this->assertStringContainsString(route('dashboard-armada.availability.store'), $html);
        $this->assertStringContainsString(route('dashboard-armada.availability.update', 1), $html);
        $route = Route::getRoutes()->getByName('dashboard-armada.index');
        $this->assertContains('auth', $route->gatherMiddleware());
        $this->assertContains('can:master-mobil-view', $route->gatherMiddleware());
        foreach (['store', 'update', 'destroy'] as $action) {
            $route = Route::getRoutes()->getByName('dashboard-armada.availability.'.$action);
            $this->assertContains('can:master-mobil-update', $route->gatherMiddleware());
        }
    }

    public function test_diagram_mode_uses_the_same_rankings_and_employee_names_and_handles_empty_data(): void
    {
        $this->gpsDays(1);
        $this->trip('B 1001 AA', '2026-10-13');
        $data = $this->dashboardData(['mode' => 'diagram']);
        $this->assertSame('diagram', $data['rankingMode']);
        $this->assertSame($this->dashboardData()['rankings']->pluck('rank')->all(), $data['rankings']->pluck('rank')->all());
        $template = str_replace(["@extends('layouts.app')", "@section('content')", '@endsection'], '', file_get_contents(resource_path('views/dashboard-armada/index.blade.php')));
        $html = Blade::render($template, $data + ['errors' => new ViewErrorBag]);
        $this->assertStringContainsString('Diagram Utilisasi Armada', $html);
        $this->assertStringContainsString('SUPIR ALEXINDO', $html);
        $this->assertStringContainsString('role="meter"', $html);
        $this->assertStringContainsString('aria-valuenow="14.29"', $html);
        $this->assertStringNotContainsString('Keterangan / Detail', $html);
        $this->assertStringContainsString('name="mode" value="diagram"', $html);
        $html = Blade::render($template, $this->dashboardData(['mode' => 'diagram', 'search' => 'TIDAK ADA']) + ['errors' => new ViewErrorBag]);
        $this->assertStringContainsString('Tidak ada armada yang sesuai', $html);
        $this->assertStringNotContainsString('role="meter"', $html);
    }
}
