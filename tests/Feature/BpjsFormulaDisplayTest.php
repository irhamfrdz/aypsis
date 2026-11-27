<?php

namespace Tests\Feature;

use App\Models\MasterRumusBpjs;
use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class BpjsFormulaDisplayTest extends TestCase
{
    public function test_shared_formula_renders_once_and_retains_individual_edit_and_delete_targets(): void
    {
        $this->actingAs((new User)->forceFill(['id' => 1, 'username' => 'admin']));
        $types = ['TUNAI', 'HARIAN', 'KANTOR', 'NON KARY-UMKM KIS', 'LAPANGAN', 'TRANSFER'];
        $records = collect($types)->map(fn ($type, $index) => (new MasterRumusBpjs)->forceFill([
            'id' => $index + 1, 'jenis' => 'jkn', 'group_name' => 'JKN-KIS', 'tipe' => $type,
            'tunjangan_persen' => '4.00', 'hutang_persen' => '1.00', 'biaya_persen' => '0',
        ]));
        $source = file_get_contents(resource_path('views/master/rumus-bpjs/index.blade.php'));
        $start = strrpos(substr($source, 0, strpos($source, '$groupRows =')), '@php');
        $end = strpos($source, '<div id="createModal"');
        $source = substr($source, $start, $end - $start);
        $html = Blade::render($source, ['rumusJkn' => $records, 'rumusJamsostek' => collect()]);

        $this->assertSame(1, substr_count($html, '4.00%'));
        $this->assertStringContainsString(implode(', ', $types), $html);
        foreach ($records as $record) {
            $this->assertStringContainsString('data-ids="'.$record->id.'"', $html);
            $this->assertStringContainsString('name="ids" value="'.$record->id.'"', $html);
        }
        $this->assertStringNotContainsString('data-ids="1,2,3,4,5,6"', $html);
        $this->assertStringNotContainsString('preserve_dimensions', $html);
    }
}
