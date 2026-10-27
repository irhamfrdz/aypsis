<?php

namespace Tests\Feature;

use App\Http\Controllers\PranotaBpjsController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PranotaBpjsRefreshTest extends TestCase
{
    public function test_refresh_returns_latest_master_data_with_distinct_employee_ids_and_no_cache(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        foreach (['karyawans', 'karyawan_tidak_tetaps'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                foreach (['nik', 'nama_lengkap', 'ktp', 'nik_ktp', 'no_ketenagakerjaan', 'jkn', 'group_jkn', 'group_bp_jamsostek', 'tipe_jkn', 'tipe_bp_jamsostek', 'dpp_jkn', 'dpp_bp_jamsostek', 'cabang_bpjs', 'penempatan', 'cabang', 'posisi', 'divisi', 'pekerjaan', 'grup', 'group'] as $column) {
                    $table->string($column)->nullable();
                }
                $table->date('tanggal_lahir')->nullable();
                $table->date('tanggal_berhenti')->nullable();
                $table->boolean('is_special_case')->default(false);
            });
        }
        Schema::create('master_rumus_bpjs', function (Blueprint $table) {
            $table->id();
            $table->string('jenis');
            $table->string('group_name');
            $table->string('nilai');
            $table->softDeletes();
        });
        DB::table('karyawans')->insert([
            ['id' => 1, 'nama_lengkap' => 'Tetap', 'tipe_jkn' => 'KANTOR', 'tipe_bp_jamsostek' => 'CREW', 'dpp_bp_jamsostek' => '1000000', 'tanggal_berhenti' => null],
            ['id' => 2, 'nama_lengkap' => 'Berhenti', 'tipe_jkn' => null, 'tipe_bp_jamsostek' => null, 'dpp_bp_jamsostek' => '1000000', 'tanggal_berhenti' => '2026-01-01'],
        ]);
        DB::table('karyawan_tidak_tetaps')->insert(['id' => 1, 'nama_lengkap' => 'Tidak Tetap']);
        DB::table('master_rumus_bpjs')->insert(['jenis' => 'jamsostek', 'group_name' => 'BPU-HARIAN', 'nilai' => '7991']);
        $request = Request::create('/pranota-bpjs/create', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
        $controller = new PranotaBpjsController;
        $before = $controller->create($request)->getData(true);
        $this->assertCount(2, $before['karyawans']);
        $this->assertSame('KANTOR', $before['karyawans'][0]['tipe_jkn']);
        $this->assertSame('CREW', $before['karyawans'][0]['tipe_bp_jamsostek']);
        $this->assertSame(['Karyawan_1', 'KaryawanTidakTetap_1'], array_column($before['karyawans'], 'unique_id'));

        DB::table('karyawans')->where('id', 1)->update(['dpp_bp_jamsostek' => '2000000', 'cabang_bpjs' => 'CILANDAK-HL']);
        DB::table('karyawan_tidak_tetaps')->where('id', 1)->update(['is_special_case' => true]);
        DB::table('master_rumus_bpjs')->update(['nilai' => '8000']);
        $response = $controller->create($request);
        $after = $response->getData(true);
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertSame('2000000', $after['karyawans'][0]['dpp_bp_jamsostek']);
        $this->assertSame('CILANDAK-HL', $after['karyawans'][0]['cabang_bpjs']);
        $this->assertTrue($after['karyawans'][1]['is_special_case']);
        $this->assertSame('8000', $after['rumusBpjs'][0]['nilai']);
        $this->assertSame('pranota-bpjs.create', $controller->create(Request::create('/'))->name());
    }
}
