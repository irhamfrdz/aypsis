<?php

namespace Tests\Feature;

use App\Http\Controllers\MasterRumusBpjsController;
use App\Models\Karyawan;
use App\Models\MasterRumusBpjs;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MasterRumusBpjsBulkUpdateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        foreach (['karyawans', 'karyawan_tidak_tetaps', 'master_rumus_bpjs'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('dpp_jkn')->nullable();
                $table->string('dpp_bp_jamsostek')->nullable();
                $table->string('jenis')->nullable();
                $table->decimal('nominal_dpp', 15, 2)->nullable();
                $table->json('hutang_tiers')->nullable();
                $table->decimal('jp_max_dpp', 15, 2)->nullable();
                $table->integer('jp_max_age')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    private function applyChange(string $field, string $old, string $new)
    {
        return (new MasterRumusBpjsController)->bulkUpdate(Request::create('/', 'POST', [
            'field' => $field, 'old_value' => $old, 'new_value' => $new,
        ]));
    }

    public function test_current_value_options_combine_employees_and_formulas_without_duplicates(): void
    {
        foreach (['2200000', '2.200.000', '', null, 'invalid', '0'] as $value) {
            DB::table('karyawans')->insert(['dpp_jkn' => $value]);
        }
        DB::table('karyawan_tidak_tetaps')->insert(['dpp_jkn' => '3000000', 'dpp_bp_jamsostek' => '2.200.000']);
        DB::table('master_rumus_bpjs')->insert([
            ['jenis' => 'jkn', 'nominal_dpp' => 2500000, 'hutang_tiers' => null, 'jp_max_age' => 99, 'jp_max_dpp' => 999, 'deleted_at' => null],
            ['jenis' => 'jamsostek', 'nominal_dpp' => null, 'hutang_tiers' => json_encode([['dpp' => 4450000], ['dpp' => 2200000]]), 'jp_max_age' => 58, 'jp_max_dpp' => 10000000, 'deleted_at' => null],
            ['jenis' => 'jamsostek', 'nominal_dpp' => null, 'hutang_tiers' => null, 'jp_max_age' => 59, 'jp_max_dpp' => 11000000, 'deleted_at' => now()],
        ]);
        $values = (new MasterRumusBpjsController)->index()->getData()['bulkValues'];
        $this->assertSame(['0.00', '2200000.00', '2500000.00', '3000000.00'], array_column($values['dpp_jkn'], 'value'));
        $this->assertSame(['2200000.00', '4450000.00'], array_column($values['dpp_bp_jamsostek'], 'value'));
        $this->assertSame([['value' => '58', 'label' => '58 tahun']], $values['jp_max_age']);
        $this->assertSame(['10000000.00'], array_column($values['jp_max_dpp'], 'value'));
        $this->assertSame('Rp 2.200.000,00', $values['dpp_jkn'][1]['label']);
    }

    public function test_dpp_updates_both_employee_tables_and_only_matching_values(): void
    {
        foreach (['karyawans', 'karyawan_tidak_tetaps'] as $table) {
            foreach (['2200000', '2.200.000', 'Rp 2.200.000,00', '2200000.00', '2500000', null, '', 'invalid'] as $value) {
                DB::table($table)->insert(['dpp_jkn' => $value, 'dpp_bp_jamsostek' => '2200000']);
            }
        }
        Karyawan::withoutEvents(fn () => $this->applyChange('dpp_jkn', '2200000', '3000000'));
        foreach (['karyawans', 'karyawan_tidak_tetaps'] as $table) {
            $this->assertSame(4, DB::table($table)->where('dpp_jkn', '3000000.00')->count());
            $this->assertSame(1, DB::table($table)->where('dpp_jkn', '2500000')->count());
            $this->assertSame(8, DB::table($table)->where('dpp_bp_jamsostek', '2200000')->count());
        }
        Karyawan::withoutEvents(fn () => $this->applyChange('dpp_bp_jamsostek', '2200000', '3100000'));
        $this->assertSame(8, DB::table('karyawans')->where('dpp_bp_jamsostek', '3100000.00')->count());
        $this->assertSame(8, DB::table('karyawan_tidak_tetaps')->where('dpp_bp_jamsostek', '3100000.00')->count());
    }

    public function test_dpp_updates_matching_formula_values_and_preserves_tier_deductions(): void
    {
        DB::table('master_rumus_bpjs')->insert([
            ['jenis' => 'jkn', 'nominal_dpp' => 2200000, 'hutang_tiers' => null, 'deleted_at' => null],
            ['jenis' => 'jkn', 'nominal_dpp' => 2500000, 'hutang_tiers' => null, 'deleted_at' => null],
            ['jenis' => 'jkn', 'nominal_dpp' => 2200000, 'hutang_tiers' => null, 'deleted_at' => now()],
            ['jenis' => 'jamsostek', 'nominal_dpp' => 2200000, 'hutang_tiers' => json_encode([
                ['dpp' => 2200000, 'potongan' => 44000, 'potongan_tanpa_diskon' => 88000],
                ['dpp' => 2500000, 'potongan' => 50000],
                ['dpp' => '2.200.000', 'potongan' => 33000],
            ]), 'deleted_at' => null],
        ]);
        MasterRumusBpjs::withoutEvents(fn () => $this->applyChange('dpp_jkn', '2200000', '3000000'));
        $this->assertEquals(3000000, MasterRumusBpjs::find(1)->nominal_dpp);
        $this->assertEquals(2500000, MasterRumusBpjs::find(2)->nominal_dpp);
        $this->assertEquals(2200000, MasterRumusBpjs::withTrashed()->find(3)->nominal_dpp);
        $this->assertEquals(2200000, MasterRumusBpjs::find(4)->nominal_dpp);

        MasterRumusBpjs::withoutEvents(fn () => $this->applyChange('dpp_bp_jamsostek', '2200000', '3000000'));
        $tiers = MasterRumusBpjs::find(4)->hutang_tiers;
        $this->assertEquals([3000000, 2500000, 3000000], array_column($tiers, 'dpp'));
        $this->assertSame([44000, 50000, 33000], array_column($tiers, 'potongan'));
        $this->assertSame(88000, $tiers[0]['potongan_tanpa_diskon']);
        $this->assertEquals(3000000, MasterRumusBpjs::find(1)->nominal_dpp);
    }

    public function test_jp_changes_only_matching_non_deleted_jamsostek_formulas(): void
    {
        DB::table('master_rumus_bpjs')->insert([
            ['jenis' => 'jamsostek', 'jp_max_age' => 58, 'jp_max_dpp' => 10000000, 'deleted_at' => null],
            ['jenis' => 'jamsostek', 'jp_max_age' => 59, 'jp_max_dpp' => 11000000, 'deleted_at' => null],
            ['jenis' => 'jkn', 'jp_max_age' => 58, 'jp_max_dpp' => 10000000, 'deleted_at' => null],
            ['jenis' => 'jamsostek', 'jp_max_age' => 58, 'jp_max_dpp' => 10000000, 'deleted_at' => now()],
        ]);
        MasterRumusBpjs::withoutEvents(function () {
            $this->applyChange('jp_max_age', '58', '60');
            $this->applyChange('jp_max_dpp', '10000000', '12000000');
        });
        $this->assertSame(60, (int) DB::table('master_rumus_bpjs')->find(1)->jp_max_age);
        $this->assertEquals(12000000, DB::table('master_rumus_bpjs')->find(1)->jp_max_dpp);
        foreach ([2 => 59, 3 => 58, 4 => 58] as $id => $age) {
            $this->assertSame($age, (int) DB::table('master_rumus_bpjs')->find($id)->jp_max_age);
        }
        $this->assertSame(0, DB::table('karyawans')->count());
    }

    public function test_invalid_field_negative_fractional_age_and_identical_values_are_rejected(): void
    {
        foreach ([['nama_lengkap', '1', '2'], ['dpp_jkn', '-1', '2'], ['jp_max_age', '58', '59.5'], ['dpp_jkn', '2200000', '2200000.00'], ['dpp_jkn', '1', '2.123']] as [$field, $old, $new]) {
            try {
                $this->applyChange($field, $old, $new);
                $this->fail('Invalid mass update must be rejected.');
            } catch (ValidationException $exception) {
                $this->assertNotEmpty($exception->errors());
            }
        }
    }
}
