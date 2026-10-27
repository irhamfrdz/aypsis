<?php

namespace Tests\Feature;

use App\Http\Controllers\GpsTrackingController;
use App\Services\GpsIdService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class GpsTrackingPortApprovalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('mobils', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_polisi');
            $table->string('imei_gps');
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->string('merek')->nullable();
            $table->string('jenis')->nullable();
        });
        Schema::create('gps_daily_distances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mobil_id');
            $table->date('date');
            $table->decimal('distance_km')->default(0);
        });
        Schema::create('surat_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('no_plat');
            $table->string('no_surat_jalan');
            $table->string('status');
            $table->date('tanggal_tanda_terima')->nullable();
            $table->timestamps();
        });
        Schema::create('tanda_terimas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_jalan_id');
            $table->string('approval_pelabuhan_status');
        });
        foreach (['surat_jalan_bongkarans', 'surat_jalan_bongkaran_batams'] as $table) {
            Schema::create($table, function (Blueprint $table) {
                $table->id();
                $table->string('no_plat');
                $table->string('status');
                $table->timestamps();
            });
        }
        foreach (['tanda_terima_bongkarans', 'tanda_terima_bongkaran_batams'] as $table) {
            Schema::create($table, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('surat_jalan_bongkaran_id');
            });
        }
    }

    public function test_muatan_remains_active_until_port_receipt_is_approved(): void
    {
        DB::table('mobils')->insert(['nomor_polisi' => 'B 1234 AY', 'imei_gps' => 'GPS-1']);
        DB::table('surat_jalans')->insert([
            ['id' => 1, 'no_plat' => 'B 1234 AY', 'no_surat_jalan' => 'SJ-TANPA-TT', 'status' => 'sudah_checkpoint', 'tanggal_tanda_terima' => null, 'created_at' => '2026-10-25 08:00:00'],
            ['id' => 2, 'no_plat' => 'B 1234 AY', 'no_surat_jalan' => 'SJ-PENDING', 'status' => 'sudah_checkpoint', 'tanggal_tanda_terima' => '2026-10-26', 'created_at' => '2026-10-26 08:00:00'],
            ['id' => 3, 'no_plat' => 'B 1234 AY', 'no_surat_jalan' => 'SJ-APPROVED', 'status' => 'sudah_checkpoint', 'tanggal_tanda_terima' => '2026-10-27', 'created_at' => '2026-10-27 08:00:00'],
        ]);
        DB::table('tanda_terimas')->insert([
            ['surat_jalan_id' => 2, 'approval_pelabuhan_status' => 'pending'],
            ['surat_jalan_id' => 3, 'approval_pelabuhan_status' => 'approved'],
        ]);

        $gpsService = Mockery::mock(GpsIdService::class);
        $gpsService->shouldReceive('getLatestLocationsBulk')->once()->with(['GPS-1'])->andReturn([
            'GPS-1' => ['status' => true, 'message' => ['data' => ['latitude' => -6.2, 'longitude' => 106.8]]],
        ]);

        $response = (new GpsTrackingController($gpsService))->getLatestLocations()->getData(true);

        $this->assertTrue($response['success']);
        $this->assertSame(
            ['SJ-PENDING', 'SJ-TANPA-TT'],
            array_column($response['data'][0]['info_sjs'], 'no_surat_jalan')
        );
    }
}
