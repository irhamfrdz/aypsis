<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class KaryawanSpecialCaseTest extends TestCase
{
    public function test_temporary_employee_termination_date_can_be_saved_with_special_case(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('karyawan_tidak_tetaps', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_masuk')->nullable();
            $table->boolean('is_special_case')->default(false);
            $table->timestamps();
        });
        $migration = require database_path('migrations/2026_10_28_130000_add_tanggal_berhenti_to_karyawan_tidak_tetaps_table.php');
        $migration->up();
        $employee = \App\Models\KaryawanTidakTetap::create([
            'tanggal_berhenti' => '2026-10-01', 'is_special_case' => true,
        ])->fresh();
        $this->assertSame('2026-10-01', $employee->tanggal_berhenti->format('Y-m-d'));
        $this->assertTrue($employee->is_special_case);
        $migration->down();
        $this->assertFalse(Schema::hasColumn('karyawan_tidak_tetaps', 'tanggal_berhenti'));
    }

    public function test_switch_value_is_saved_and_restored_in_the_form(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('status_pajak')->nullable();
            $table->timestamps();
        });
        $migration = require database_path('migrations/2026_10_28_120000_add_is_special_case_to_karyawans_table.php');
        $migration->up();
        Karyawan::withoutEvents(function () {
            $employee = Karyawan::create([]);
            $this->assertFalse($employee->fresh()->is_special_case);
            $employee->update(['is_special_case' => '1']);
            $this->assertTrue($employee->fresh()->is_special_case);
            $html = view('karyawan-tidak-tetap.partials.special-case-switch', [
                'specialCaseEmployee' => $employee->fresh(), 'errors' => new ViewErrorBag,
            ])->render();
            $this->assertMatchesRegularExpression('/id="is_special_case"[^>]*checked/', $html);
            $employee->update(['is_special_case' => '0']);
            $this->assertFalse($employee->fresh()->is_special_case);
        });
        $migration->down();
        $this->assertFalse(Schema::hasColumn('karyawans', 'is_special_case'));
    }
}
