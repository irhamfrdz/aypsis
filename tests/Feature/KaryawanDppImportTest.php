<?php

namespace Tests\Feature;

use App\Http\Controllers\KaryawanController;
use App\Imports\KaryawanDppImport;
use App\Models\Karyawan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class KaryawanDppImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            foreach (['nik', 'nama_lengkap', 'group_jkn', 'tipe_jkn', 'dpp_jkn', 'group_bp_jamsostek', 'tipe_bp_jamsostek', 'dpp_bp_jamsostek', 'cabang_bpjs'] as $column) {
                $table->string($column)->nullable();
            }
            $table->timestamps();
        });
        Schema::create('master_rumus_bpjs', function (Blueprint $table) {
            $table->id();
            $table->string('jenis');
            $table->string('group_name');
            $table->string('tipe');
            $table->softDeletes();
        });
        DB::table('karyawans')->insert(['nik' => '01593', 'nama_lengkap' => 'ADITYA', 'dpp_jkn' => '1000000']);
        DB::table('master_rumus_bpjs')->insert(['jenis' => 'jamsostek', 'group_name' => 'BPU-ALEXINDO PLUIT', 'tipe' => 'CREW']);
    }

    public function test_template_headers_and_excel_import_round_trip_include_types_in_requested_order(): void
    {
        ob_start();
        (new KaryawanController)->downloadDppTemplate()->sendContent();
        $template = ob_get_clean();
        $header = explode("\r\n", substr($template, 3))[0];
        $this->assertSame('nik;nama;Group JKN;Tipe JKN;DPP JKN;Group BP Jamsostek;Tipe BP Jamsostek;DPP BP Jamsostek;Cabang BPJS', $header);

        $path = tempnam(sys_get_temp_dir(), 'bpjs-import-');
        try {
            file_put_contents($path, $header."\r\n01593;ADITYA;JKN-KIS-KANTOR;KARYAWAN;3200000;BPU-ALEXINDO PLUIT;CREW;4100000;PLUIT\r\n");
            $import = new KaryawanDppImport;
            Karyawan::withoutEvents(fn () => Excel::import($import, $path, null, \Maatwebsite\Excel\Excel::CSV));
            $this->assertSame([], $import->failedRows);
            $this->assertSame(1, $import->successCount);
            $employee = Karyawan::first();
            $this->assertSame('01593', $employee->nik);
            $this->assertSame('KARYAWAN', $employee->tipe_jkn);
            $this->assertSame('CREW', $employee->tipe_bp_jamsostek);
            $this->assertSame('3200000', $employee->dpp_jkn);
            $this->assertSame('4100000', $employee->dpp_bp_jamsostek);
            $this->assertSame('PLUIT', $employee->cabang_bpjs);
        } finally {
            unlink($path);
        }
    }

    public function test_invalid_group_type_pair_does_not_save_other_fields(): void
    {
        $import = new KaryawanDppImport;
        Karyawan::withoutEvents(fn () => $import->collection(collect([[
            'nik' => '01593', 'dpp_jkn' => '3200000',
            'group_bp_jamsostek' => 'BPU-ALEXINDO PLUIT', 'tipe_bp_jamsostek' => 'KANTOR',
        ]])));
        $this->assertSame(0, $import->successCount);
        $this->assertCount(1, $import->failedRows);
        $this->assertSame('1000000', Karyawan::first()->dpp_jkn);
        $this->assertNull(Karyawan::first()->tipe_bp_jamsostek);
    }

    public function test_old_template_without_type_columns_preserves_existing_types(): void
    {
        DB::table('karyawans')->update(['tipe_jkn' => 'KARYAWAN', 'tipe_bp_jamsostek' => 'CREW']);
        $import = new KaryawanDppImport;
        Karyawan::withoutEvents(fn () => $import->collection(collect([[
            'nik' => '01593', 'nama_karyawan' => 'ADITYA', 'dpp_jkn' => '3200000',
        ]])));
        $this->assertSame([], $import->failedRows);
        $this->assertSame(1, $import->successCount);
        $this->assertSame('KARYAWAN', Karyawan::first()->tipe_jkn);
        $this->assertSame('CREW', Karyawan::first()->tipe_bp_jamsostek);
    }

    public function test_import_matches_the_type_category_even_when_group_names_are_shared(): void
    {
        DB::table('master_rumus_bpjs')->insert([
            ['jenis' => 'jamsostek', 'group_name' => 'BPU-ALEXINDO PLUIT', 'tipe' => 'LAPANGAN'],
            ['jenis' => 'jamsostek', 'group_name' => 'PPU-CREW', 'tipe' => 'CREW'],
            ['jenis' => 'jamsostek', 'group_name' => 'PPU-KANTOR', 'tipe' => 'KANTOR'],
        ]);

        $cases = [
            ['BPU-ALEXINDO PLUIT', ' crew ', true, 'BPU-CREW'],
            ['BPU-ALEXINDO PLUIT', 'LAPANGAN', true, 'Non BPU-CREW'],
            ['PPU-CREW', 'CREW', false, 'BPU-CREW'],
            ['PPU-KANTOR', 'KANTOR', false, 'Non BPU-CREW'],
        ];

        foreach ($cases as [$group, $type, $accepted, $categoryLabel]) {
            DB::table('karyawans')->update([
                'group_bp_jamsostek' => null, 'tipe_bp_jamsostek' => null, 'dpp_jkn' => '1000000',
            ]);
            $import = new KaryawanDppImport;
            Karyawan::withoutEvents(fn () => $import->collection(collect([[
                'nik' => '01593', 'group_bp_jamsostek' => $group,
                'tipe_bp_jamsostek' => $type, 'dpp_jkn' => '3200000',
            ]])));

            $this->assertSame($accepted ? 1 : 0, $import->successCount, "$group / $type");
            if ($accepted) {
                $this->assertSame([], $import->failedRows);
                $this->assertSame(trim($type), Karyawan::first()->tipe_bp_jamsostek);
            } else {
                $this->assertCount(1, $import->failedRows);
                $this->assertStringContainsString('kategori '.$categoryLabel, $import->failedRows[0]['reason']);
                $this->assertSame('1000000', Karyawan::first()->dpp_jkn);
                $this->assertNull(Karyawan::first()->group_bp_jamsostek);
            }
        }
    }
}
