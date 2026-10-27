<?php

namespace Tests\Unit;

use App\Models\MasterRumusBpjs;
use PHPUnit\Framework\TestCase;

class MasterRumusBpjsCategoryTest extends TestCase
{
    public function test_category_uses_crew_type_and_preserves_legacy_groups(): void
    {
        $cases = [
            ['BPU-ALEXINDO PLUIT', 'CREW', 'bpu_crew'],
            ['BPU-ALEXINDO PLUIT', ' crew ', 'bpu_crew'],
            ['BPU-ALEXINDO PLUIT', 'BPU-CREW', 'bpu_crew'],
            ['BPU-CREW', null, 'bpu_crew'],
            ['BPU-CREW', 'NON CREW', 'bpu_non_crew'],
            ['BPU-HARIAN', null, 'bpu_non_crew'],
            ['PPU-KANTOR', 'CREW', 'ppu'],
        ];

        foreach ($cases as [$group, $tipe, $expected]) {
            $rumus = new MasterRumusBpjs(['group_name' => $group, 'tipe' => $tipe]);
            $this->assertSame($expected, $rumus->jamsostekCategory(), "$group / $tipe");
        }
    }
}
