<?php

namespace Tests\Unit;

use App\Models\MasterRumusBpjs;
use App\Services\BpjsFormulaGroups;
use PHPUnit\Framework\TestCase;

class BpjsFormulaGroupsTest extends TestCase
{
    private function formula(int $id, string $tipe, array $changes = []): MasterRumusBpjs
    {
        return (new MasterRumusBpjs)->forceFill(array_merge([
            'id' => $id, 'jenis' => 'jkn', 'group_name' => 'BPJS JKN-KIS',
            'tipe' => $tipe, 'tunjangan_persen' => '4.00', 'hutang_persen' => '1.00',
            'biaya_persen' => '0', 'cabang_bpjs' => null,
        ], $changes));
    }

    public function test_identical_formulas_for_six_types_share_one_row(): void
    {
        $types = ['TUNAI', 'HARIAN', 'KANTOR', 'NON KARY-UMKM KIS', 'LAPANGAN', 'TRANSFER'];
        $records = collect($types)->map(fn ($type, $index) => $this->formula($index + 1, $type));
        $groups = BpjsFormulaGroups::group($records);

        $this->assertCount(1, $groups);
        $this->assertSame($types, $groups->first()->pluck('tipe')->all());
        $this->assertCount(6, $records);
    }

    public function test_different_rates_groups_branches_exceptions_and_schedules_stay_separate(): void
    {
        foreach ([
            ['hutang_persen' => '2'],
            ['nominal_dpp' => '5000000'],
            ['group_name' => 'GROUP LAIN'],
            ['cabang_bpjs' => 'BTM'],
            ['diskon_pengecualian' => ['Karyawan_1']],
            ['bpjs_effective_values' => [['component' => 'dpp_jkn', 'valid_from' => '2027-01-01', 'valid_until' => '2027-12-31', 'values' => ['nominal_dpp' => 5000000]]]],
            ['employee_targets' => ['Karyawan_1']],
        ] as $change) {
            $this->assertCount(2, BpjsFormulaGroups::group(collect([
                $this->formula(1, 'TUNAI'), $this->formula(2, 'KANTOR', $change),
            ])));
        }
    }

    public function test_numeric_formatting_and_audit_fields_do_not_split_the_same_formula(): void
    {
        $this->assertCount(1, BpjsFormulaGroups::group(collect([
            $this->formula(1, 'TUNAI', ['created_by' => 1]),
            $this->formula(2, 'TRANSFER', ['tunjangan_persen' => '4', 'created_by' => 2]),
        ])));
    }

    public function test_display_group_keeps_the_original_action_ids_separate(): void
    {
        $actions = collect([
            collect([$this->formula(1, 'TUNAI')]),
            collect([$this->formula(2, 'KANTOR')]),
        ]);
        $display = BpjsFormulaGroups::display($actions);
        $this->assertCount(1, $display);
        $this->assertSame([[1], [2]], $display->first()->map(fn ($items) => $items->pluck('id')->all())->values()->all());
    }
}
