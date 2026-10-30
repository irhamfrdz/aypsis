<?php

namespace Tests\Feature;

use App\Http\Controllers\MasterRumusBpjsController;
use App\Models\MasterRumusBpjs;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MasterRumusBpjsDiscountTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('master_rumus_bpjs', function (Blueprint $table) {
            $table->id();
            foreach ((new MasterRumusBpjs)->getFillable() as $field) {
                if ($field !== 'diskon_pengecualian') {
                    $table->string($field)->nullable();
                }
            }
            $table->timestamps();
            $table->softDeletes();
        });
        (require database_path('migrations/2026_10_30_160000_add_discount_exclusions_to_master_rumus_bpjs_table.php'))->up();
        foreach (['karyawans', 'karyawan_tidak_tetaps'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                foreach (['nik', 'nama_lengkap', 'group_bp_jamsostek', 'tipe_bp_jamsostek', 'cabang_bpjs'] as $field) {
                    $table->string($field)->nullable();
                }
            });
        }
        DB::table('master_rumus_bpjs')->insert([
            ['id' => 1, 'jenis' => 'jamsostek', 'group_name' => 'BPU-ALEXINDO PLUIT', 'tipe' => 'CREW', 'cabang_bpjs' => 'PLUIT', 'diskon_status' => 'ada', 'diskon_nilai' => 50],
            ['id' => 2, 'jenis' => 'jamsostek', 'group_name' => 'CILANDAK-HL CREW', 'tipe' => 'CREW', 'cabang_bpjs' => 'PLUIT', 'diskon_status' => 'ada', 'diskon_nilai' => 50],
        ]);
        DB::table('karyawans')->insert([
            ['id' => 1, 'nik' => '001', 'nama_lengkap' => 'Tetap Pluit', 'group_bp_jamsostek' => ' bpu-alexindo pluit ', 'tipe_bp_jamsostek' => 'Crew', 'cabang_bpjs' => 'PLUIT'],
            ['id' => 2, 'nik' => '002', 'nama_lengkap' => 'Group Lain', 'group_bp_jamsostek' => 'PPU', 'tipe_bp_jamsostek' => 'CREW', 'cabang_bpjs' => 'PLUIT'],
            ['id' => 3, 'nik' => '003', 'nama_lengkap' => 'Tipe Lain', 'group_bp_jamsostek' => 'BPU-ALEXINDO PLUIT', 'tipe_bp_jamsostek' => 'TUNAI', 'cabang_bpjs' => 'PLUIT'],
            ['id' => 4, 'nik' => '004', 'nama_lengkap' => 'Cabang Lain', 'group_bp_jamsostek' => 'BPU-ALEXINDO PLUIT', 'tipe_bp_jamsostek' => 'CREW', 'cabang_bpjs' => 'JKT'],
        ]);
        DB::table('karyawan_tidak_tetaps')->insert([
            ['id' => 1, 'nik' => '001', 'nama_lengkap' => 'Tidak Tetap Pluit', 'group_bp_jamsostek' => 'BPU-ALEXINDO PLUIT', 'tipe_bp_jamsostek' => 'CREW', 'cabang_bpjs' => 'PLUIT'],
            ['id' => 2, 'nik' => '002', 'nama_lengkap' => 'Tidak Tetap Cilandak', 'group_bp_jamsostek' => 'CILANDAK-HL CREW', 'tipe_bp_jamsostek' => 'CREW', 'cabang_bpjs' => 'PLUIT'],
        ]);
    }

    public function test_candidates_and_saved_exclusions_follow_group_type_branch_and_employee_model(): void
    {
        MasterRumusBpjs::withoutEvents(function () {
            $controller = new MasterRumusBpjsController;
            $data = $controller->discountEmployees(Request::create('/', 'GET'), 1)->getData(true);
            $this->assertEqualsCanonicalizing(['Karyawan_1', 'KaryawanTidakTetap_1'], array_column($data['employees'], 'unique_id'));
            $controller->saveDiscountExclusions(Request::create('/', 'PUT', [
                'existing_ids' => '1,2', 'diskon_pengecualian' => ['Karyawan_1', 'KaryawanTidakTetap_2'],
            ]), 1);
            $this->assertSame(['Karyawan_1'], MasterRumusBpjs::find(1)->diskon_pengecualian);
            $this->assertSame(['KaryawanTidakTetap_2'], MasterRumusBpjs::find(2)->diskon_pengecualian);
            $this->assertEquals(50, MasterRumusBpjs::find(1)->diskon_nilai);
            $saved = $controller->discountEmployees(Request::create('/', 'GET', ['existing_ids' => '1,2']), 1)->getData(true);
            $this->assertEqualsCanonicalizing(['Karyawan_1', 'KaryawanTidakTetap_2'], $saved['selected']);
            $controller->saveDiscountExclusions(Request::create('/', 'PUT', ['diskon_pengecualian' => []]), 1);
            $this->assertSame([], MasterRumusBpjs::find(1)->diskon_pengecualian);
            $this->assertSame(['KaryawanTidakTetap_2'], MasterRumusBpjs::find(2)->diskon_pengecualian);
        });
    }

    public function test_rejects_employees_outside_group_or_type_and_unrelated_formula_ids(): void
    {
        $controller = new MasterRumusBpjsController;
        foreach (['Karyawan_2', 'Karyawan_3', 'Karyawan_4', 'KaryawanTidakTetap_2'] as $key) {
            try {
                $controller->saveDiscountExclusions(Request::create('/', 'PUT', ['diskon_pengecualian' => [$key]]), 1);
                $this->fail('An employee outside this formula must be rejected.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('diskon_pengecualian', $exception->errors());
            }
        }
        $this->assertNull(MasterRumusBpjs::find(1)->diskon_pengecualian);
        DB::table('master_rumus_bpjs')->where('id', 2)->update(['tipe' => 'TUNAI']);
        $this->expectException(ValidationException::class);
        $controller->discountEmployees(Request::create('/', 'GET', ['existing_ids' => '1,2']), 1);
    }

    public function test_candidates_use_the_effective_branch_formula_and_reject_ambiguous_legacy_types(): void
    {
        DB::table('master_rumus_bpjs')->where('id', 1)->update(['cabang_bpjs' => null]);
        DB::table('master_rumus_bpjs')->insert([
            'id' => 3, 'jenis' => 'jamsostek', 'group_name' => 'BPU-ALEXINDO PLUIT', 'tipe' => 'CREW', 'cabang_bpjs' => 'PLUIT',
        ]);
        DB::table('karyawans')->insert([
            'id' => 5, 'nama_lengkap' => 'Tipe Lama Kosong', 'group_bp_jamsostek' => 'BPU-ALEXINDO PLUIT', 'cabang_bpjs' => 'JKT',
        ]);
        $controller = new MasterRumusBpjsController;
        $data = $controller->discountEmployees(Request::create('/', 'GET'), 1)->getData(true);
        $this->assertEqualsCanonicalizing(['Karyawan_4', 'Karyawan_5'], array_column($data['employees'], 'unique_id'));
        $specific = $controller->discountEmployees(Request::create('/', 'GET'), 3)->getData(true);
        $this->assertEqualsCanonicalizing(['Karyawan_1', 'KaryawanTidakTetap_1'], array_column($specific['employees'], 'unique_id'));
        DB::table('master_rumus_bpjs')->insert([
            'id' => 4, 'jenis' => 'jamsostek', 'group_name' => 'BPU-ALEXINDO PLUIT', 'tipe' => 'TUNAI',
        ]);
        $data = $controller->discountEmployees(Request::create('/', 'GET'), 1)->getData(true);
        $this->assertSame(['Karyawan_4'], array_column($data['employees'], 'unique_id'));
    }
}
