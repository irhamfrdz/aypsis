<?php

namespace Tests\Feature;

use App\Http\Controllers\DivisiController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class DivisiExportTest extends TestCase
{
    public function test_download_includes_all_filtered_rows_and_preserves_codes_as_text(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('divisis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_divisi');
            $table->string('kode_divisi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active');
        });

        for ($i = 1; $i <= 17; $i++) {
            DB::table('divisis')->insert([
                'nama_divisi' => sprintf('Operasional %02d', $i),
                'kode_divisi' => sprintf('%03d', $i),
                'deskripsi' => '=1+1',
                'is_active' => true,
            ]);
        }

        DB::table('divisis')->insert([
            ['nama_divisi' => 'Operasional Lama', 'kode_divisi' => 'OLD', 'is_active' => false],
            ['nama_divisi' => 'Keuangan', 'kode_divisi' => 'FIN', 'is_active' => true],
        ]);

        $response = (new DivisiController)->export(Request::create('/master/divisi/export', 'GET', [
            'search' => 'Operasional', 'status' => 'active', 'page' => 2,
        ]));
        $path = $response->getFile()->getPathname();

        try {
            $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition'));
            $sheet = IOFactory::load($path)->getActiveSheet();
            $this->assertSame(['No', 'Nama Divisi', 'Kode Divisi', 'Deskripsi', 'Status'], $sheet->rangeToArray('A1:E1')[0]);
            $this->assertSame(18, $sheet->getHighestRow());
            $this->assertSame('Operasional 01', $sheet->getCell('B2')->getValue());
            $this->assertSame('001', $sheet->getCell('C2')->getValue());
            $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('C2')->getDataType());
            $this->assertSame('=1+1', $sheet->getCell('D2')->getValue());
            $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('D2')->getDataType());
            $this->assertSame('Aktif', $sheet->getCell('E2')->getValue());
            $this->assertEquals(17, $sheet->getCell('A18')->getValue());
            $this->assertSame('Operasional 17', $sheet->getCell('B18')->getValue());
        } finally {
            unlink($path);
        }
    }

    public function test_export_route_matches_before_divisi_show_and_requires_export_permission(): void
    {
        $route = app('router')->getRoutes()->match(Request::create('/master/divisi/export'));

        $this->assertSame('master.divisi.export', $route->getName());
        $this->assertContains('can:master-divisi-export', $route->gatherMiddleware());
        $this->assertContains('auth', $route->gatherMiddleware());
    }
}
