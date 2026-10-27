<?php

namespace Tests\Feature;

use App\Models\MasterRumusBpjs;
use App\Rules\JamsostekTypeMatchesGroup;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class KaryawanJamsostekOptionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('master_rumus_bpjs', function (Blueprint $table) {
            $table->id();
            $table->string('jenis');
            $table->string('group_name');
            $table->string('tipe')->nullable();
            $table->softDeletes();
        });
        DB::table('master_rumus_bpjs')->insert([
            ['jenis' => 'jamsostek', 'group_name' => 'BPU-ALEXINDO PLUIT', 'tipe' => 'CREW', 'deleted_at' => null],
            ['jenis' => 'jamsostek', 'group_name' => 'BPU-ALEXINDO PLUIT', 'tipe' => ' crew ', 'deleted_at' => null],
            ['jenis' => 'jamsostek', 'group_name' => 'PPU-KANTOR', 'tipe' => 'KANTOR', 'deleted_at' => null],
            ['jenis' => 'jkn', 'group_name' => 'BPU-ALEXINDO PLUIT', 'tipe' => 'JKN ONLY', 'deleted_at' => null],
            ['jenis' => 'jamsostek', 'group_name' => 'BPU-ALEXINDO PLUIT', 'tipe' => 'DELETED', 'deleted_at' => '2026-01-01 00:00:00'],
        ]);
    }

    private function accepts(?string $group, ?string $type): bool
    {
        return Validator::make(['tipe' => $type], [
            'tipe' => ['nullable', 'string', new JamsostekTypeMatchesGroup($group)],
        ])->passes();
    }

    public function test_only_types_for_the_selected_jamsostek_group_are_accepted(): void
    {
        $this->assertTrue($this->accepts(' bpu-alexindo  pluit ', ' crew '));
        $this->assertTrue($this->accepts('PPU-KANTOR', 'KANTOR'));
        $this->assertFalse($this->accepts('PPU-KANTOR', 'CREW'));
        $this->assertFalse($this->accepts('BPU-ALEXINDO PLUIT', 'JKN ONLY'));
        $this->assertFalse($this->accepts('BPU-ALEXINDO PLUIT', 'DELETED'));
        $this->assertFalse($this->accepts(null, 'CREW'));
        $this->assertTrue($this->accepts(null, null));
    }

    public function test_edit_dropdown_contains_only_matching_types_and_selects_the_saved_value(): void
    {
        $html = view('master-karyawan.partials.jamsostek-type-select', [
            'jamsostekRumusOptions' => MasterRumusBpjs::where('jenis', 'jamsostek')->get(['group_name', 'tipe']),
            'currentGroup' => 'bpu-alexindo pluit',
            'currentType' => 'crew',
            'labelClasses' => '', 'selectClasses' => '', 'errors' => new ViewErrorBag,
        ])->render();

        $this->assertSame(1, substr_count($html, 'value="CREW"'));
        $this->assertMatchesRegularExpression('/value="CREW"\s+selected/', $html);
        $this->assertStringNotContainsString('value="KANTOR"', $html);
        $this->assertStringNotContainsString('value="JKN ONLY"', $html);
        $this->assertStringNotContainsString('value="DELETED"', $html);
    }
}
