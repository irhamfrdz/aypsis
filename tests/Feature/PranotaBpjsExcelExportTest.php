<?php

namespace Tests\Feature;

use App\Http\Controllers\PranotaBpjsController;
use App\Models\PranotaBpjsHeader;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PranotaBpjsExcelExportTest extends TestCase
{
    public function test_export_keeps_template_columns_and_maps_saved_employee_values(): void
    {
        $this->createPranotaSchema();
        DB::table('karyawans')->insert(['id' => 1, 'cabang_bpjs' => 'JKT']);
        $header = PranotaBpjsHeader::create([
            'nomor_pranota' => 'PBPJS26110003', 'tanggal_pranota' => '2026-11-04',
            'periode_bulan' => 11, 'periode_tahun' => 2026, 'status' => 'draft',
        ]);
        $header->details()->create([
            'tipe_karyawan' => \App\Models\Karyawan::class, 'karyawan_id' => 1,
            'bpjs_kesehatan' => 229195.20, 'bpjs_ketenagakerjaan' => 57298.80,
            'jht_biaya' => 100000.80, 'jht_hutang' => 20000, 'jkk_tunjangan' => 1000,
            'jkm_tunjangan' => 2000, 'jp_biaya' => 10000, 'jp_hutang' => 11000,
            'total' => 470494.80, 'rincian_bpjs' => [
                'jht_biaya' => 100000.80, 'jht_hutang' => 20000, 'jkk_tunjangan' => 1000,
                'jkm_tunjangan' => 2000, 'jp_biaya' => 10000, 'jp_hutang' => 11000,
                'jkk_hutang' => 3000, 'bpu_jkk_tunjangan' => 4000, 'bpu_jkm' => 5000,
            ],
            'snapshot_bpjs' => [
                'karyawan' => ['id' => 1, 'nik' => '0012', 'nama_lengkap' => 'ABDULLAH',
                    'no_ketenagakerjaan' => '3202160607820012', 'nik_ktp' => '3202160607820012',
                    'tanggal_lahir' => '1982-07-06', 'tanggal_masuk' => '2020-01-01',
                    'group_jkn' => 'JKN-KIS', 'group_bp_jamsostek' => 'PPU', 'tipe_jkn' => 'KANTOR',
                    'tipe_bp_jamsostek' => 'KARYAWAN', 'dpp_bp_jamsostek' => '4.450.000', 'pekerjaan' => 'SUPIR TRUCK', 'cabang_bpjs' => 'PLUIT'],
                'kis_tunjangan' => 229195.20, 'kis_biaya' => 0,
                'ringkasan' => ['ppu' => 3000, 'bpu' => 9000, 'jkn' => 229195.20,
                    'kary_jht' => 54000, 'kary_jkn' => 57298.80, 'pt' => 110000.80, 'non_kary' => 0],
            ],
            'tunjangan_manual' => ['ppu' => 3000, 'bpu' => 9000], 'no_voucher' => '0001/VC-BPJS',
        ]);

        $original = $header->details()->first();
        foreach (['5.360.000,50', '4450000.00', '0'] as $wage) {
            $copy = $original->replicate();
            $snapshot = $copy->snapshot_bpjs;
            $snapshot['karyawan']['dpp_bp_jamsostek'] = $wage;
            $copy->snapshot_bpjs = $snapshot;
            $copy->save();
        }
        $legacy = $original->replicate();
        $legacy->rincian_bpjs = null;
        $legacy->save();

        $response = (new PranotaBpjsController)->exportExcel($header);
        $path = $response->getFile()->getPathname();
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName('Rincian BPJS');
        $template = \PhpOffice\PhpSpreadsheet\IOFactory::load(base_path('BPJS 26 11.xlsx'))->getSheet(0);

        $this->assertNotNull($sheet);
        $this->assertSame('NIK. KARY.', $sheet->getCell('B1')->getValue());
        $this->assertSame('NAMA KARYAWAN', $sheet->getCell('C1')->getValue());
        $this->assertSame($template->getCell('I1')->getValue(), $sheet->getCell('H1')->getValue());
        $this->assertSame($template->getCell('H1')->getValue(), $sheet->getCell('I1')->getValue());
        $this->assertSame('ABDULLAH', $sheet->getCell('C2')->getValue());
        $this->assertSame('0012', $sheet->getCell('B2')->getValue());
        $this->assertSame('3202160607820012', $sheet->getCell('D2')->getValue());
        $this->assertSame('PPU', $sheet->getCell('H2')->getValue());
        $this->assertSame('KARYAWAN', $sheet->getCell('I2')->getValue());
        $this->assertSame('JKT', $sheet->getCell('J2')->getValue());
        $this->assertSame(4450000.0, $sheet->getCell('L2')->getValue());
        $this->assertEquals(5360000.5, $sheet->getCell('L4')->getValue());
        $this->assertEquals(4450000, $sheet->getCell('L6')->getValue());
        $this->assertEquals(0, $sheet->getCell('L8')->getValue());
        $this->assertSame('0.00', \PhpOffice\PhpSpreadsheet\Style\NumberFormat::toFormattedString(0, $sheet->getStyle('L2')->getNumberFormat()->getFormatCode()));
        $this->assertSame(100000.8, $sheet->getCell('M2')->getValue());
        $this->assertNull($sheet->getCell('AA2')->getValue());
        $this->assertSame(229195.2, $sheet->getCell('AA3')->getValue());
        $this->assertSame('ABDULLAH', $sheet->getCell('C3')->getValue());
        $this->assertSame('JKN-KIS', $sheet->getCell('H3')->getValue());
        $this->assertSame('KANTOR', $sheet->getCell('I3')->getValue());
        $this->assertNull($sheet->getCell('M3')->getValue());
        $this->assertSame('0001/VC-BPJS', $sheet->getCell('AL2')->getValue());
        $this->assertSame('AKUN JURNAL', $sheet->getCell('BC1')->getValue());
        $this->assertSame('Belum dipetakan', $sheet->getCell('BC2')->getValue());
        $this->assertEquals(12000, $sheet->getCell('BD2')->getValue());
        $this->assertEquals(110000.8, $sheet->getCell('BE2')->getValue());
        $this->assertEquals(34000, $sheet->getCell('BF2')->getValue());
        $this->assertEquals(229195.2, $sheet->getCell('BD3')->getValue());
        $this->assertEquals(57298.8, $sheet->getCell('BF3')->getValue());
        $this->assertSame('GROUP JKN', $sheet->getCell('BG1')->getValue());
        $this->assertSame('GROUP BP JAMSOSTEK', $sheet->getCell('BH1')->getValue());
        $this->assertSame('JKN-KIS', $sheet->getCell('BG2')->getValue());
        $this->assertSame('PPU', $sheet->getCell('BH2')->getValue());
        $this->assertNull($sheet->getCell('BI2')->getValue());
        $this->assertEquals(144000.8, $sheet->getCell('BI10')->getValue());
        @unlink($path);
    }

    private function createPranotaSchema(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('cabang_bpjs')->nullable();
        });
        Schema::create('pranota_bpjs_headers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota')->nullable();
            $table->date('tanggal_pranota')->nullable();
            $table->unsignedInteger('periode_bulan')->nullable();
            $table->unsignedInteger('periode_tahun')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->decimal('total_bpjs_kesehatan', 18, 2)->default(0);
            $table->decimal('total_bpjs_ketenagakerjaan', 18, 2)->default(0);
            $table->decimal('grand_total', 18, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('pranota_bpjs_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_bpjs_header_id');
            $table->unsignedBigInteger('karyawan_id');
            $table->string('tipe_karyawan');
            foreach (['bpjs_kesehatan', 'bpjs_ketenagakerjaan', 'jht_biaya', 'jht_hutang', 'jkk_tunjangan', 'jkm_tunjangan', 'jp_biaya', 'jp_hutang', 'total'] as $column) {
                $table->decimal($column, 18, 2)->default(0);
            }
            $table->timestamps();
        });
        (require database_path('migrations/2026_10_28_160000_add_rincian_bpjs_to_pranota_bpjs_details_table.php'))->up();
        (require database_path('migrations/2026_10_28_170000_add_tunjangan_manual_to_pranota_bpjs_details_table.php'))->up();
        (require database_path('migrations/2026_10_29_100000_add_no_voucher_to_pranota_bpjs_details_table.php'))->up();
        (require database_path('migrations/2026_10_30_100000_add_snapshots_to_pranota_bpjs_tables.php'))->up();
    }
}
