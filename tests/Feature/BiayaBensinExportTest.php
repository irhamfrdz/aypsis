<?php

namespace Tests\Feature;

use App\Http\Controllers\BiayaBensinController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class BiayaBensinExportTest extends TestCase
{
    public function test_excel_download_contains_only_records_in_selected_date_range(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('biaya_bensin', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->unsignedBigInteger('mobil_id')->nullable();
            $table->unsignedBigInteger('alat_berat_id')->nullable();
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->string('nomor_kartu')->nullable();
            $table->unsignedInteger('km_awal')->nullable();
            $table->unsignedInteger('km_akhir')->nullable();
            $table->decimal('liter', 12, 2)->nullable();
            $table->decimal('harga_per_liter', 15, 2)->nullable();
            $table->decimal('biaya', 15, 2)->nullable();
            $table->string('status')->nullable();
            $table->text('keterangan')->nullable();
        });

        foreach (['2026-10-31', '2026-11-01', '2026-11-02'] as $date) {
            DB::table('biaya_bensin')->insert([
                'tanggal' => $date,
                'nomor_kartu' => $date,
                'liter' => 10,
                'harga_per_liter' => 10000,
                'biaya' => 100000,
                'status' => 'approved',
            ]);
        }

        $response = (new BiayaBensinController)->exportExcel(Request::create('/export', 'GET', [
            'export_start_date' => '2026-11-01',
            'export_end_date' => '2026-11-01',
        ]));

        $sheet = IOFactory::load($response->getFile()->getPathname())->getActiveSheet();
        $this->assertSame('Tanggal', $sheet->getCell('A1')->getValue());
        $this->assertSame('01/11/2026', $sheet->getCell('A2')->getValue());
        $this->assertSame('2026-11-01', $sheet->getCell('C2')->getValue());
        $this->assertSame(2, $sheet->getHighestRow());
    }
}
