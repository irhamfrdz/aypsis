<?php

namespace Tests\Feature;

use App\Http\Controllers\AbsensiImportController;
use App\Http\Controllers\Api\ApiAttendanceController;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AttendanceOvertimeInputTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->string('nik');
            $table->dateTime('waktu');
            $table->string('tipe');
            foreach (['verify_mode', 'keterangan', 'foto', 'device', 'detail_lokasi', 'status'] as $column) {
                $table->string($column)->nullable();
            }
            $table->decimal('latitude')->nullable();
            $table->decimal('longitude')->nullable();
            $table->timestamps();
            $table->unique(['nik', 'waktu', 'tipe']);
        });
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
        });
        Schema::create('absensi_deletion_marks', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->dateTime('waktu');
            $table->string('tipe');
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('auditable_id');
            foreach (['user_name', 'auditable_type', 'action', 'module', 'description', 'ip_address', 'user_agent', 'url'] as $column) {
                $table->string($column)->nullable();
            }
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('lokasi_absensis', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active');
            $table->string('tipe_penugasan')->nullable();
        });
        Schema::create('lokasi_absensi_karyawan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->unsignedBigInteger('lokasi_absensi_id');
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_dat_import_preserves_machine_overtime_codes_four_and_five(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'attendance-overtime-');
        file_put_contents($path, "1001\t2026-10-22 17:00:00\t1\t1\n1001\t2026-10-22 18:00:00\t4\t1\n1001\t2026-10-23 04:00:00\t5\t1\n");
        try {
            $method = new \ReflectionMethod(AbsensiImportController::class, 'parseDatFile');
            $count = Absensi::withoutEvents(fn () => $method->invoke(app(AbsensiImportController::class), $path));
            $this->assertSame(3, $count);
            $this->assertSame(['Pulang', 'lembur_masuk', 'lembur_pulang'], Absensi::orderBy('waktu')->pluck('tipe')->all());
            $this->assertSame(3, Absensi::workDates('2026-10-22')->count());
        } finally {
            unlink($path);
        }
    }

    public function test_api_accepts_overnight_overtime_and_deduplicates_the_work_session(): void
    {
        DB::table('absensis')->insert([
            ['karyawan_id' => 1, 'nik' => '1001', 'waktu' => '2026-10-22 17:00:00', 'tipe' => 'Pulang'],
            ['karyawan_id' => 1, 'nik' => '1001', 'waktu' => '2026-10-22 18:00:00', 'tipe' => 'lembur_masuk'],
        ]);
        $user = new User(['karyawan_id' => 1]);
        $employee = new Karyawan(['nik' => '1001']);
        $employee->id = 1;
        $user->setRelation('karyawan', $employee);
        $request = Request::create('/api/attendance', 'POST', ['tipe' => 'Lembur_Pulang']);
        $request->setUserResolver(fn () => $user);
        Carbon::setTestNow(Carbon::parse('2026-10-23 04:00:00', 'Asia/Jakarta'));

        $controller = app(ApiAttendanceController::class);
        $response = Absensi::withoutEvents(fn () => $controller->store($request));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('lembur_pulang', $response->getData(true)['data']['tipe']);
        $this->assertSame(1, Absensi::where('tipe', 'Pulang')->count());
        $this->assertSame(3, Absensi::workDates('2026-10-22')->count());

        $request->merge(['tipe' => 'Pulang']);
        $duplicate = $controller->store($request);
        $this->assertSame(400, $duplicate->getStatusCode());
        $this->assertSame(3, Absensi::count());
    }

    public function test_excel_import_accepts_named_overtime_types(): void
    {
        $import = new \App\Imports\AbsensiImport;
        Absensi::withoutEvents(fn () => $import->collection(collect([
            collect(['1001', '2026-10-22 17:00:00', 'Pulang']),
            collect(['1001', '2026-10-22 18:00:00', 'Lembur_Masuk']),
            collect(['1001', '2026-10-23 04:00:00', 'Lembur_Pulang']),
        ])));
        $this->assertSame(3, $import->importedCount);
        $this->assertSame(['Pulang', 'lembur_masuk', 'lembur_pulang'], Absensi::orderBy('waktu')->pluck('tipe')->all());
    }

    public function test_repair_is_previewed_audited_and_keeps_regular_checkout_and_timestamps(): void
    {
        DB::table('absensis')->insert([
            ['karyawan_id' => 1, 'nik' => '1001', 'waktu' => '2026-10-22 17:00:00', 'tipe' => 'Pulang'],
            ['karyawan_id' => 1, 'nik' => '1001', 'waktu' => '2026-10-22 18:00:00', 'tipe' => 'lembur_masuk'],
            ['karyawan_id' => 1, 'nik' => '1001', 'waktu' => '2026-10-23 04:00:00', 'tipe' => 'Pulang'],
        ]);
        $this->artisan('absensi:repair-overtime-checkouts')
            ->expectsOutputToContain('1 scan teridentifikasi')->assertExitCode(0);
        $this->assertSame(2, Absensi::where('tipe', 'Pulang')->count());
        $this->artisan('absensi:repair-overtime-checkouts', ['--apply' => true])
            ->expectsOutputToContain('1 scan diperbaiki')->assertExitCode(0);
        $this->assertSame('Pulang', Absensi::where('waktu', '2026-10-22 17:00:00')->value('tipe'));
        $this->assertSame('lembur_pulang', Absensi::where('waktu', '2026-10-23 04:00:00')->value('tipe'));
        $this->assertSame(3, Absensi::count());
        $this->assertSame(1, DB::table('audit_logs')->count());
        $this->artisan('absensi:repair-overtime-checkouts')
            ->expectsOutputToContain('0 scan teridentifikasi')->assertExitCode(0);
    }

    public function test_repair_handles_an_existing_overtime_log_at_the_same_timestamp_without_deleting_records(): void
    {
        DB::table('absensis')->insert([
            ['karyawan_id' => 1, 'nik' => '1001', 'waktu' => '2026-10-22 17:00:00', 'tipe' => 'Pulang'],
            ['karyawan_id' => 1, 'nik' => '1001', 'waktu' => '2026-10-22 18:00:00', 'tipe' => 'lembur_masuk'],
            ['karyawan_id' => 1, 'nik' => '1001', 'waktu' => '2026-10-23 04:00:00', 'tipe' => 'Pulang'],
            ['karyawan_id' => 1, 'nik' => '1001', 'waktu' => '2026-10-23 04:00:00', 'tipe' => 'lembur_pulang'],
        ]);
        $this->artisan('absensi:repair-overtime-checkouts', ['--apply' => true])
            ->expectsOutputToContain('1 scan diperbaiki')->assertExitCode(0);
        $this->assertSame(4, Absensi::count());
        $this->assertSame(1, Absensi::where('tipe', 'Pulang')->count());
        $this->assertSame(2, Absensi::where('waktu', '2026-10-23 04:00:00')->count());
        $this->assertSame('Pulang', Absensi::where('waktu', '2026-10-22 17:00:00')->value('tipe'));
    }
}
