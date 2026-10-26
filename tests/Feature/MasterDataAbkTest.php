<?php

namespace Tests\Feature;

use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\UserController;
use App\Models\Permission;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MasterDataAbkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            foreach (['nik', 'nama_lengkap', 'divisi', 'pekerjaan', 'cabang'] as $column) {
                $table->string($column)->nullable();
            }
            $table->date('tanggal_berhenti')->nullable();
        });

        DB::table('karyawans')->insert([
            ['nik' => '001', 'nama_lengkap' => 'Awak Divisi', 'divisi' => 'ABK', 'pekerjaan' => 'NAHKODA', 'cabang' => 'JAKARTA', 'tanggal_berhenti' => null],
            ['nik' => '002', 'nama_lengkap' => 'Awak Pekerjaan', 'divisi' => 'OPERASIONAL', 'pekerjaan' => 'ABK', 'cabang' => 'BATAM', 'tanggal_berhenti' => null],
            ['nik' => '003', 'nama_lengkap' => 'Awak Berhenti', 'divisi' => 'ABK', 'pekerjaan' => 'ABK', 'cabang' => 'BATAM', 'tanggal_berhenti' => '2026-10-01'],
            ['nik' => '004', 'nama_lengkap' => 'Staf Kantor', 'divisi' => 'KANTOR', 'pekerjaan' => 'ADMIN', 'cabang' => 'JAKARTA', 'tanggal_berhenti' => null],
        ]);
    }

    public function test_abk_list_includes_division_and_job_matches_only(): void
    {
        $data = (new KaryawanController)->abkIndex(Request::create('/'))->getData();

        $this->assertSame(['001', '002'], $data['karyawans']->pluck('nik')->all());
        $this->assertSame(['total' => 3, 'aktif' => 2, 'berhenti' => 1], $data['counts']);
    }

    public function test_branch_and_status_filters_apply_to_both_abk_matches(): void
    {
        $controller = new KaryawanController;
        $data = $controller->abkIndex(Request::create('/', 'GET', ['cabang' => 'BATAM']))->getData();
        $this->assertSame(['002'], $data['karyawans']->pluck('nik')->all());

        $data = $controller->abkIndex(Request::create('/', 'GET', ['show_berhenti' => '1']))->getData();
        $this->assertSame(['003'], $data['karyawans']->pluck('nik')->all());
    }

    public function test_abk_permissions_save_and_reload_without_selecting_general_employee_permissions(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        $actions = ['view', 'create', 'update', 'delete', 'print', 'export'];
        foreach ($actions as $action) {
            DB::table('permissions')->insert(['name' => 'master-karyawan-'.$action]);
            DB::table('permissions')->insert(['name' => 'master-karyawan-abk-'.$action]);
        }
        DB::table('permissions')->insert(['name' => 'master.karyawan.index']);

        $controller = new UserController;
        $save = new \ReflectionMethod($controller, 'convertMatrixPermissionsToIds');
        $load = new \ReflectionMethod($controller, 'convertPermissionsToMatrix');
        $matrix = ['master-karyawan-abk' => array_fill_keys($actions, '1')];
        $ids = $save->invoke($controller, $matrix);
        $names = Permission::whereIn('id', $ids)->pluck('name')->all();

        $this->assertEqualsCanonicalizing(array_map(fn ($action) => 'master-karyawan-abk-'.$action, $actions), $names);
        $this->assertSame(['master-karyawan-abk' => array_fill_keys($actions, true)], $load->invoke($controller, $names));

        $ids = $save->invoke($controller, ['master-karyawan-abk' => ['view' => '1', 'create' => '0']]);
        $this->assertSame(['master-karyawan-abk-view'], Permission::whereIn('id', $ids)->pluck('name')->all());
        $this->assertSame([], $save->invoke($controller, ['master-karyawan-abk' => ['view' => '0']]));
    }
}
