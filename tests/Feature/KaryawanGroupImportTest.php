<?php

namespace Tests\Feature;

use App\Imports\KaryawanGroupImport;
use App\Models\Karyawan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class KaryawanGroupImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->json('grup')->nullable();
            $table->json('grup_bpjs')->nullable();
            $table->timestamps();
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
    }

    private function employee(?array $groups, string $nik = '0228'): Karyawan
    {
        $id = DB::table('karyawans')->insertGetId([
            'nik' => $nik, 'grup' => $groups === null ? null : json_encode($groups),
            'grup_bpjs' => '["BPJS:LAMA"]',
        ]);

        return Karyawan::findOrFail($id);
    }

    private function runImport(array $rows): KaryawanGroupImport
    {
        $import = new KaryawanGroupImport;
        $import->collection(collect($rows)->map(fn ($row) => collect($row)));

        return $import;
    }

    public function test_import_appends_salary_group_and_preserves_existing_meal_group(): void
    {
        $employee = $this->employee(['UANG MAKAN:KANTOR BATAM']);
        $other = $this->employee(['LEMBUR:PELABUHAN'], '1004');
        $import = $this->runImport([['nik' => '0228', 'group' => 'Gaji', 'sub_group' => 'ABK - A1']]);
        $this->assertSame(['UANG MAKAN:KANTOR BATAM', 'GAJI:ABK - A1'], $employee->fresh()->grup);
        $this->assertSame(['BPJS:LAMA'], $employee->fresh()->grup_bpjs);
        $this->assertSame(['LEMBUR:PELABUHAN'], $other->fresh()->grup);
        $this->assertSame(1, $import->successCount);
        $this->assertSame([], $import->failedRows);
        $audit = DB::table('audit_logs')->first();
        $this->assertSame('updated', $audit->action);
        $this->assertSame(['UANG MAKAN:KANTOR BATAM'], json_decode($audit->old_values, true)['grup']);
    }

    public function test_reimport_and_mixed_case_existing_pairs_do_not_create_duplicates(): void
    {
        $employee = $this->employee(['Uang Makan : Kantor Batam']);
        $rows = [
            ['nik' => '0228', 'group' => 'UANG MAKAN', 'sub_group' => 'KANTOR BATAM'],
            ['nik' => '0228', 'group' => 'GAJI', 'sub_group' => 'ABK - A1'],
            ['nik' => '0228', 'group' => 'GAJI:ABK - A1'],
        ];
        $this->runImport($rows);
        $this->runImport($rows);
        $this->assertSame(['Uang Makan : Kantor Batam', 'GAJI:ABK - A1'], $employee->fresh()->grup);
        $this->assertSame(1, DB::table('audit_logs')->count());
    }

    public function test_blank_groups_do_not_erase_existing_data(): void
    {
        $employee = $this->employee(['UANG MAKAN:KANTOR BATAM']);
        $this->runImport([
            ['nik' => '0228', 'group' => '', 'sub_group' => ''],
            ['nik' => '0228', 'sub_group' => 'ABK - A1'],
        ]);
        $this->assertSame(['UANG MAKAN:KANTOR BATAM'], $employee->fresh()->grup);
        $this->assertSame(0, DB::table('audit_logs')->count());
    }

    public function test_multiple_rows_delimiters_and_same_group_with_another_subgroup_are_appended(): void
    {
        $employee = $this->employee(['UANG MAKAN:KANTOR BATAM', 'GAJI:ABK - A2']);
        $import = $this->runImport([
            ['nik' => '0228', 'group' => 'GAJI;LEMBUR', 'sub_group' => 'ABK - A1;PELABUHAN'],
            ['nik' => '0228', 'grup' => 'TRANSPORTASI:KANTOR BATAM'],
        ]);
        $this->assertSame(['UANG MAKAN:KANTOR BATAM', 'GAJI:ABK - A2', 'GAJI:ABK - A1', 'LEMBUR:PELABUHAN', 'TRANSPORTASI:KANTOR BATAM'], $employee->fresh()->grup);
        $this->assertSame(1, $import->successCount);
    }

    public function test_employee_without_groups_can_receive_new_groups(): void
    {
        $employee = $this->employee(null);
        $this->runImport([['nik' => '0228', 'group' => 'GAJI', 'subgroup' => 'ABK - A1']]);
        $this->assertSame(['GAJI:ABK - A1'], $employee->fresh()->grup);
    }

    public function test_clear_commands_are_rejected_without_erasing_or_partially_updating_groups(): void
    {
        $employee = $this->employee(['UANG MAKAN:KANTOR BATAM']);
        foreach (['CLEAR', 'HAPUS', '-', 'KOSONG', 'NULL'] as $command) {
            $import = $this->runImport([
                ['nik' => '0228', 'group' => 'GAJI', 'sub_group' => 'ABK - A1'],
                ['nik' => '0228', 'group' => $command],
            ]);
            $this->assertSame(0, $import->successCount);
            $this->assertCount(1, $import->failedRows);
            $this->assertSame(['UANG MAKAN:KANTOR BATAM'], $employee->fresh()->grup);
        }
    }

    public function test_unknown_or_missing_nik_is_reported_without_affecting_other_rows(): void
    {
        $employee = $this->employee(['UANG MAKAN:KANTOR BATAM']);
        $import = $this->runImport([
            ['nik' => '', 'group' => 'GAJI'],
            ['nik' => '9999', 'group' => 'GAJI'],
            ['nik' => '0228', 'group' => 'GAJI', 'sub_group' => 'ABK - A1'],
        ]);
        $this->assertSame(1, $import->successCount);
        $this->assertCount(2, $import->failedRows);
        $this->assertSame(['UANG MAKAN:KANTOR BATAM', 'GAJI:ABK - A1'], $employee->fresh()->grup);
    }
}
