<?php

namespace Tests\Feature;

use App\Http\Controllers\KaryawanTidakTetapController;
use App\Models\KaryawanTidakTetap;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class KaryawanTidakTetapSalaryGroupsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        (require database_path('migrations/2026_10_24_100000_create_type_gaji_karyawan_table.php'))->up();
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->json('grup')->nullable();
            $table->string('penempatan')->nullable();
        });
        Schema::create('karyawan_tidak_tetaps', function (Blueprint $table) {
            $table->id();
            $table->string('group_jkn')->nullable();
            $table->string('group_bp_jamsostek')->nullable();
            $table->string('nik');
            $table->string('nama_lengkap');
            $table->json('group')->nullable();
            $table->json('sub_group')->nullable();
            $table->timestamps();
        });
        foreach (['pekerjaans', 'pajaks'] as $tableName) {
            Schema::create($tableName, fn (Blueprint $table) => $table->id());
        }
        DB::table('type_gaji_karyawan')->insert([
            ['nama' => 'ABK - A1', 'status' => 'active'],
            ['nama' => 'TUNAI', 'status' => 'active'],
            ['nama' => 'TRANSFER', 'status' => 'inactive'],
        ]);
        DB::table('karyawans')->insert(['grup' => '["GAJI:LAMA", "UANG MAKAN:KANTOR BATAM"]']);
    }

    public function test_custom_bpjs_groups_are_saved_updated_and_available_on_both_forms(): void
    {
        $controller = new KaryawanTidakTetapController;
        $controller->store(Request::create('/', 'POST', [
            'nik' => 'P0001', 'nama_lengkap' => 'Karyawan A',
            'group_jkn' => ' jkn-group   baru ', 'group_bp_jamsostek' => ' ppu-group baru ',
        ]));
        $employee = KaryawanTidakTetap::firstOrFail();
        $this->assertSame('JKN-GROUP BARU', $employee->group_jkn);
        $this->assertSame('PPU-GROUP BARU', $employee->group_bp_jamsostek);
        foreach ([$controller->create(), $controller->edit($employee)] as $view) {
            $options = $view->getData()['bpjsGroupOptions'];
            $this->assertContains('JKN-GROUP BARU', $options['group_jkn']);
            $this->assertContains('PPU-GROUP BARU', $options['group_bp_jamsostek']);
            $this->assertContains('JKN-KIS-KANTOR', $options['group_jkn']);
        }
        $controller->update(Request::create('/', 'PUT', [
            'nik' => 'P0001', 'nama_lengkap' => 'Karyawan A',
            'group_jkn' => ' jkn-baru edit ', 'group_bp_jamsostek' => ' ppu-baru edit ',
        ]), $employee);
        $this->assertSame('JKN-BARU EDIT', $employee->fresh()->group_jkn);
        $this->assertSame('PPU-BARU EDIT', $employee->fresh()->group_bp_jamsostek);
        $this->assertContains('JKN-BARU EDIT', $controller->create()->getData()['bpjsGroupOptions']['group_jkn']);
    }

    public function test_create_and_edit_use_active_salary_master_options(): void
    {
        $controller = new KaryawanTidakTetapController;
        $employee = new KaryawanTidakTetap(['nik' => 'P0001', 'nama_lengkap' => 'Karyawan A', 'group' => ['GAJI'], 'sub_group' => []]);
        foreach ([$controller->create(), $controller->edit($employee)] as $view) {
            $data = $view->getData();
            $this->assertTrue($data['groups']->contains('GAJI'));
            $this->assertSame(['ABK - A1', 'TUNAI'], $data['subGroupsByGroup']['GAJI']);
            $this->assertSame(['KANTOR BATAM'], $data['subGroupsByGroup']['UANG MAKAN']);
        }
    }

    public function test_edit_preserves_existing_selection_and_filters_other_group_options(): void
    {
        $employee = new KaryawanTidakTetap(['nik' => 'P0001', 'nama_lengkap' => 'Karyawan A', 'group' => ['GAJI'], 'sub_group' => ['TRANSFER']]);
        $data = (new KaryawanTidakTetapController)->edit($employee)->getData();
        $html = view('karyawan-tidak-tetap.partials.group-fields', array_merge($data, ['labelClasses' => '', 'selectClasses' => '']))->render();
        $this->assertStringContainsString('value="ABK - A1"', $html);
        $this->assertStringContainsString('value="TRANSFER" selected', $html);
        $this->assertStringNotContainsString('value="LAMA"', $html);
        $this->assertStringNotContainsString('value="KANTOR BATAM"', $html);

        $employee->group = ['GAJI', 'UANG MAKAN'];
        $data['karyawanTidakTetap'] = $employee;
        $multi = view('karyawan-tidak-tetap.partials.group-fields', array_merge($data, ['labelClasses' => '', 'selectClasses' => '']))->render();
        $this->assertStringContainsString('value="KANTOR BATAM"', $multi);
    }

    public function test_salary_group_is_available_even_without_employee_history(): void
    {
        DB::table('karyawans')->delete();
        $data = (new KaryawanTidakTetapController)->create()->getData();
        $this->assertSame(['GAJI'], $data['groups']->all());
        $this->assertSame(['ABK - A1', 'TUNAI'], $data['subGroupsByGroup']['GAJI']);
        $html = view('karyawan-tidak-tetap.partials.group-fields', array_merge($data, ['labelClasses' => '', 'selectClasses' => '']))->render();
        $this->assertStringContainsString('value="GAJI"', $html);
    }

    public function test_selected_master_salary_subgroup_is_saved_and_can_be_updated(): void
    {
        $controller = new KaryawanTidakTetapController;
        $controller->store(Request::create('/', 'POST', [
            'nik' => 'P0001', 'nama_lengkap' => 'Karyawan A', 'group' => ['GAJI'], 'sub_group' => ['ABK - A1'],
        ]));
        $employee = KaryawanTidakTetap::firstOrFail();
        $this->assertSame(['GAJI'], $employee->group);
        $this->assertSame(['ABK - A1'], $employee->sub_group);
        $controller->update(Request::create('/', 'PUT', [
            'nik' => 'P0001', 'nama_lengkap' => 'Karyawan A', 'group' => ['GAJI'], 'sub_group' => ['TUNAI'],
        ]), $employee);
        $this->assertSame(['TUNAI'], $employee->fresh()->sub_group);
    }
}
