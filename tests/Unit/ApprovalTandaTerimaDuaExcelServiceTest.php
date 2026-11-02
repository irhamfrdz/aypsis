<?php

namespace Tests\Unit;

use App\Models\ShipperConsignee;
use App\Services\ApprovalTandaTerimaDuaExcelService;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Tests\TestCase;

class ApprovalTandaTerimaDuaExcelServiceTest extends TestCase
{
    public function test_export_removes_company_header_and_sets_sheet_view(): void
    {
        $receipt = (new class extends Model {})->forceFill(['no_surat_jalan' => 'TEST-001']);
        $receipt->setRelation('shipperJb', (new ShipperConsignee)->forceFill(['document_ppftz_03' => 'AYP']));
        $otherReceipt = clone $receipt;
        $otherReceipt->setRelation('shipperJb', (new ShipperConsignee)->forceFill(['document_ppftz_03' => 'LAINNYA']));
        $path = (new ApprovalTandaTerimaDuaExcelService)->createMany('fcl', [
            ['receipt' => $receipt],
            ['receipt' => $otherReceipt],
        ]);

        try {
            $workbook = IOFactory::load($path);
            $sheet = $workbook->getSheet(0);

            $this->assertSame('M A N I F E S T    O F    C A R G O', $sheet->getCell('B1')->getValue());
            $this->assertSame('B/L NO.', $sheet->getCell('B5')->getValue());
            $this->assertSame('TEST-001', $sheet->getCell('AC7')->getValue());
            $this->assertSame('TEST-001', $sheet->getCell('AC8')->getValue());
            $this->assertSame('AYP', $sheet->getCell('AA7')->getValue());
            $this->assertSame('FFFCA5A5', $sheet->getStyle('AA7')->getFill()->getStartColor()->getARGB());
            $this->assertSame('LAINNYA', $sheet->getCell('AA8')->getValue());
            $this->assertSame(Fill::FILL_NONE, $sheet->getStyle('AA8')->getFill()->getFillType());
            $this->assertSame(70, $sheet->getSheetView()->getZoomScale());
            $this->assertSame('A7', $sheet->getFreezePane());

            $workbook->disconnectWorksheets();
        } finally {
            unlink($path);
        }
    }
}
