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

class MasterRumusBpjsTipeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        (require database_path('migrations/2026_11_25_160000_create_bpjs_effective_values_table.php'))->up();
        Schema::create('master_rumus_bpjs', function (Blueprint $table) {
            $table->id();
            foreach ((new MasterRumusBpjs)->getFillable() as $column) {
                if ($column !== 'tipe') {
                    $table->string($column)->nullable();
                }
            }
            $table->timestamps();
            $table->softDeletes();
        });

        $migration = require database_path('migrations/2026_10_27_094000_add_tipe_to_master_rumus_bpjs_table.php');
        $migration->up();
    }

    public function test_store_requires_tipe_for_every_row(): void
    {
        $request = Request::create('/', 'POST', [
            'jenis' => ['jkn', 'jamsostek'],
            'group_name' => [['JKN-KIS-TUNAI'], ['BPU-CREW']],
            'tipe' => ['Karyawan'],
        ]);

        try {
            (new MasterRumusBpjsController)->store($request);
            $this->fail('A row without tipe must be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('tipe.1', $exception->errors());
            $this->assertSame(0, MasterRumusBpjs::count());
        }
    }

    public function test_create_schedules_dpp_and_discount_with_separate_dates(): void
    {
        MasterRumusBpjs::withoutEvents(function () {
            (new MasterRumusBpjsController)->store(Request::create('/', 'POST', [
                'jenis' => ['jkn'], 'group_name' => ['JKN-KIS'], 'tipe' => ['KANTOR'],
                'nominal_dpp' => ['3000000'], 'dpp_from' => ['2026-12-01'], 'dpp_until' => ['2027-03-31'],
                'diskon_status' => ['ada'], 'diskon_tipe' => ['persen'], 'diskon_nilai' => ['25'],
                'diskon_from' => ['2027-01-01'], 'diskon_until' => ['2027-02-28'],
            ]));
        });
        $record = MasterRumusBpjs::first();
        $this->assertNull($record->nominal_dpp);
        $this->assertSame('tidak_ada', $record->diskon_status);
        $this->assertSame(2, DB::table('bpjs_effective_values')->count());
        $schedule = DB::table('bpjs_effective_values')->where('component', 'dpp_jkn')->first();
        $this->assertEquals(3000000, json_decode($schedule->values, true)['nominal_dpp']);
    }

    public function test_tipe_is_saved_for_all_selected_groups_and_updated(): void
    {
        MasterRumusBpjs::withoutEvents(function () {
            $controller = new MasterRumusBpjsController;
            $controller->store(Request::create('/', 'POST', [
                'jenis' => ['jkn'],
                'group_name' => ['JKN-KIS-TUNAI, JKN-KIS-KANTOR'],
                'tipe' => ['Karyawan'],
                'nominal_dpp' => ['5000000.50'],
            ]));

            $records = MasterRumusBpjs::all();
            $this->assertCount(2, $records);
            $this->assertSame(['Karyawan', 'Karyawan'], $records->pluck('tipe')->all());
            $this->assertSame(['5000000.50', '5000000.50'], $records->pluck('nominal_dpp')->all());

            $controller->update(Request::create('/', 'PUT', [
                'group_name' => 'JKN-KIS-TUNAI, JKN-KIS-KANTOR',
                'existing_ids' => $records->pluck('id')->implode(','),
                'tipe' => 'Non Karyawan',
                'nominal_dpp' => '6000000',
            ]), $records->first()->id);

            $this->assertSame(['Non Karyawan', 'Non Karyawan'], MasterRumusBpjs::pluck('tipe')->all());
            $this->assertSame(['6000000.00', '6000000.00'], MasterRumusBpjs::pluck('nominal_dpp')->all());
        });
    }

    public function test_edit_requires_tipe(): void
    {
        $this->expectException(ValidationException::class);
        (new MasterRumusBpjsController)->update(Request::create('/', 'PUT', [
            'group_name' => ['JKN-KIS-TUNAI'],
            'tipe' => '',
        ]), 1);
    }

    public function test_personal_formula_saves_distinct_employee_types_and_rejects_another_group(): void
    {
        foreach (['karyawans', 'karyawan_tidak_tetaps'] as $table) {
            Schema::create($table, function (Blueprint $table) {
                $table->id();
                foreach (['nik', 'nama_lengkap', 'group_jkn', 'group_bp_jamsostek', 'tipe_jkn', 'tipe_bp_jamsostek', 'cabang_bpjs'] as $column) {
                    $table->string($column)->nullable();
                }
            });
            DB::table($table)->insert(['id' => 1, 'nama_lengkap' => 'Anggota', 'group_jkn' => 'JKN-KIS', 'tipe_jkn' => 'KANTOR']);
            DB::table($table)->insert(['id' => 2, 'nama_lengkap' => 'Lain', 'group_jkn' => 'GROUP LAIN', 'tipe_jkn' => 'KANTOR']);
        }
        $data = ['jenis' => ['jkn'], 'group_name' => ['JKN-KIS'], 'tipe' => ['KANTOR'],
            'employee_targets' => [json_encode(['Karyawan_1', 'KaryawanTidakTetap_1'])]];
        MasterRumusBpjs::withoutEvents(fn () => (new MasterRumusBpjsController)->store(Request::create('/', 'POST', $data)));
        $this->assertSame(['Karyawan_1', 'KaryawanTidakTetap_1'], MasterRumusBpjs::first()->employee_targets);
        $data['employee_targets'] = [json_encode(['Karyawan_2'])];
        try {
            (new MasterRumusBpjsController)->store(Request::create('/', 'POST', $data));
            $this->fail('An employee from another group must be rejected');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('employee_targets', $exception->errors());
            $this->assertSame(1, MasterRumusBpjs::count());
        }
    }

    public function test_diskon_update_can_omit_tipe_for_legacy_records(): void
    {
        $record = MasterRumusBpjs::withoutEvents(fn () => MasterRumusBpjs::create([
            'jenis' => 'jamsostek', 'group_name' => 'BPU-CREW',
        ]));

        $request = Request::create('/', 'PUT', ['diskon_status' => 'ada', 'diskon_nilai' => 10]);
        $request->headers->set('Accept', 'application/json');
        $response = (new MasterRumusBpjsController)->update($request, $record->id);

        $this->assertTrue($response->getData(true)['success']);
        $this->assertSame('10', $record->fresh()->diskon_nilai);
        $this->assertNull($record->fresh()->tipe);
    }
}
