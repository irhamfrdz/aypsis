<?php

namespace Tests\Feature;

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Api\AbsensiSyncController;
use App\Imports\AbsensiImport;
use App\Services\AbsensiDeletionGuard;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AbsensiDestroyDayTest extends TestCase
{
    public function test_destroy_day_deletes_matching_database_logs_including_overnight_checkout(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->string('nik');
            $table->dateTime('waktu');
            $table->date('tanggal_kerja_manual')->nullable();
            $table->string('tipe');
        });
        (require database_path('migrations/2026_10_15_110000_create_absensi_deletion_marks_table.php'))->up();
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
        });

        DB::table('absensis')->insert([
            ['nik' => '1523', 'waktu' => '2026-10-15 07:32:00', 'tipe' => 'Masuk'],
            ['nik' => '1523', 'waktu' => '2026-10-15 17:00:00', 'tipe' => 'Pulang'],
            ['nik' => '1523', 'waktu' => '2026-10-16 02:00:00', 'tipe' => 'Pulang'],
            ['nik' => '1523', 'waktu' => '2026-10-16 07:35:00', 'tipe' => 'Masuk'],
            ['nik' => '9999', 'waktu' => '2026-10-15 08:00:00', 'tipe' => 'Masuk'],
        ]);

        $request = Request::create('/absensi/destroy-day', 'DELETE', [
            'nik' => '1523',
            'tanggal' => '2026-10-15',
        ]);
        $this->app->instance('request', $request);

        (new AbsensiController)->destroyDay($request);

        $this->assertSame(0, DB::table('absensis')->where('nik', '1523')->whereIn('waktu', [
            '2026-10-15 07:32:00',
            '2026-10-15 17:00:00',
            '2026-10-16 02:00:00',
        ])->count());
        $this->assertSame(2, DB::table('absensis')->count());
        $this->assertSame(3, DB::table('absensi_deletion_marks')->where('nik', '1523')->count());
        $this->assertTrue(AbsensiDeletionGuard::isDeleted('1523', '2026-10-16 02:00:00', 'Keluar'));

        config(['app.sync_secret' => 'test-secret']);
        $syncRequest = Request::create('/api/absensi/sync', 'POST', [
            'mesin_id' => 1,
            'logs' => [[
                'nik' => '1523',
                'waktu' => '2026-10-16 02:00:00',
                'tipe' => 'Pulang',
            ]],
        ], [], [], ['HTTP_X_SYNC_SECRET' => 'test-secret']);
        $response = (new AbsensiSyncController)->push($syncRequest);

        $this->assertSame(0, $response->getData(true)['synced_count']);
        $this->assertSame(2, DB::table('absensis')->count());

        $import = new AbsensiImport;
        $import->collection(collect([collect(['1523', '2026-10-16 02:00:00', 'Pulang'])]));
        $this->assertSame(0, $import->importedCount);
        $this->assertSame(2, DB::table('absensis')->count());

        $deleteLogRequest = Request::create('/absensi/delete-log', 'DELETE', [
            'nik' => '1523',
            'tanggal' => '2026-10-16',
            'tipe' => 'Masuk',
        ]);
        $this->app->instance('request', $deleteLogRequest);
        (new AbsensiController)->deleteLog($deleteLogRequest);

        $this->assertSame(1, DB::table('absensis')->count());
        $this->assertSame(4, DB::table('absensi_deletion_marks')->where('nik', '1523')->count());
    }
}
