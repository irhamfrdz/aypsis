<?php

namespace Tests\Feature;

use App\Http\Controllers\StockKontainerPergudangController;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class StockKontainerPergudangTest extends TestCase
{
    public function test_export_laporan()
    {
        Excel::fake();

        $fileName = 'Laporan_Persediaan_Kontainer_'.date('Ymd_His').'.xlsx';

        $controller = new StockKontainerPergudangController;
        $response = $controller->exportLaporan();

        Excel::assertDownloaded($fileName, function ($export) {
            return $export instanceof \App\Exports\LaporanPersediaanKontainerExport;
        });
    }
}
