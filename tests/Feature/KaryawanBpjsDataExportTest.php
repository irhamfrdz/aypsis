<?php

namespace Tests\Feature;

use App\Exports\KaryawanBpjsDataExport;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class KaryawanBpjsDataExportTest extends TestCase
{
    public function test_export_contains_employee_bpjs_fields_and_preserves_nik(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            foreach (['nik', 'nama_lengkap', 'group_jkn', 'tipe_jkn', 'dpp_jkn', 'group_bp_jamsostek', 'tipe_bp_jamsostek', 'dpp_bp_jamsostek', 'cabang_bpjs'] as $field) {
                $table->string($field)->nullable();
            }
            $table->softDeletes();
        });
        DB::table('karyawans')->insert(['nik' => '0012', 'nama_lengkap' => 'ADI', 'group_jkn' => 'JKN-KIS', 'tipe_jkn' => 'KANTOR', 'dpp_jkn' => '2200000', 'group_bp_jamsostek' => 'PPU', 'tipe_bp_jamsostek' => 'KANTOR', 'dpp_bp_jamsostek' => '4450000', 'cabang_bpjs' => 'JKT']);

        $path = tempnam(sys_get_temp_dir(), 'bpjs-test-');
        try {
            file_put_contents($path, Excel::raw(new KaryawanBpjsDataExport, \Maatwebsite\Excel\Excel::XLSX));
            $sheet = IOFactory::load($path)->getActiveSheet();
            $this->assertSame(['0012', 'ADI', 'JKN-KIS', 'KANTOR', 2200000, 'PPU', 'KANTOR', 4450000, 'JKT'], array_values($sheet->rangeToArray('A2:I2', null, true, false)[0]));
        } finally {
            @unlink($path);
        }
    }
}
