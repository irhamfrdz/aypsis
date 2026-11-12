<?php

namespace App\Services;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ManifestSugengExcelService
{
    public function create(Collection $items): string
    {
        $book = new Spreadsheet;
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('MANIFEST & PERINCIAN');
        $ship = $items->first()->nama_kapal;
        $voyage = $items->first()->no_voyage;
        $sheet->setCellValue('B1', 'PT. ALEXINDO YAKIN PRIMA');
        $sheet->setCellValue('B2', 'Perusahaan Pelayaran Nasional');
        $sheet->setCellValue('B3', 'M A N I F E S T    O F    C A R G O');
        $sheet->setCellValue('B4', 'Kapal: '.$ship.'    Voyage: '.$voyage);
        $sheet->mergeCells('B1:AG1');
        $sheet->mergeCells('B2:AG2');
        $sheet->mergeCells('B3:AG3');
        $sheet->mergeCells('B4:AG4');
        $sheet->setCellValue('I6', 'MANIFEST');
        $sheet->setCellValue('L6', 'PERINCIAN');
        $headers = [
            'B' => 'B/L NO.', 'C' => 'HS CODE', 'D' => 'MARK AND NUMBERS', 'E' => 'SEAL NO.',
            'F' => 'JUMLAH KONTAINER', 'G' => 'SATUAN', 'H' => 'TIPE / SIZE',
            'I' => 'JUMLAH', 'J' => 'SATUAN', 'K' => 'DESCRIPTION OF GOODS',
            'L' => 'JUMLAH', 'M' => 'SATUAN', 'N' => 'DESCRIPTION OF GOODS',
            'O' => 'WEIGHT', 'P' => 'MEASS', 'Q' => 'SHIPPER', 'R' => 'ADDRESS',
            'S' => 'NO. IDENTITAS (NPWP SHIPPER)', 'T' => 'CONSIGNEE', 'U' => 'ADDRESS',
            'V' => 'NO. IDENTITAS (NPWP CONSIGNEE)', 'W' => 'NOTIFY PARTY (CONSIGNEE)',
            'X' => 'ADDRESS', 'Y' => 'NO. IDENTITAS (NPWP NOTIFY PARTY)',
            'Z' => 'DELIVERY ADDRESS & CONTACT PERSON', 'AA' => 'DOCUMENT PPFTZ-03',
            'AB' => 'CONDITION', 'AC' => 'RECEIPT SIGNS & ROAD LETTERS', 'AD' => 'DATE',
            'AE' => 'JUMLAH', 'AF' => 'SATUAN', 'AG' => 'KETERANGAN PERHITUNGAN M3 / KGS',
        ];
        foreach ($headers as $column => $label) {
            $sheet->setCellValue($column.'7', $label);
        }
        $sheet->getStyle('B7:AG7')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('B7:AG7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('243B64');
        $sheet->getStyle('B7:AG7')->getAlignment()->setWrapText(true);
        $sheet->getRowDimension(7)->setRowHeight(42);

        $row = 8;
        $lastContainer = null;
        foreach ($items as $item) {
            $container = trim((string) $item->nomor_kontainer);
            $isLcl = $item->source_type === 'lcl' || str_contains(strtoupper((string) $item->tipe_kontainer), 'LCL');
            if ($isLcl && $container !== '' && strtoupper($container) !== $lastContainer) {
                $sheet->setCellValue('D'.$row, $container);
                $sheet->setCellValue('E'.$row, $item->no_seal);
                $sheet->setCellValue('F'.$row, 1);
                $sheet->setCellValue('G'.$row, 'Unit');
                $sheet->setCellValue('H'.$row, trim($item->tipe_kontainer.' '.$item->size_kontainer));
                $sheet->setCellValue('AE'.$row, 1);
                $sheet->setCellValue('AF'.$row, 'Unit');
                $sheet->setCellValue('AG'.$row, trim($item->tipe_kontainer.' '.$item->size_kontainer));
                foreach (($item->sheet_values['container'] ?? []) as $column => $value) {
                    if (array_key_exists($column, $headers)) {
                        $sheet->setCellValueExplicit($column.$row, (string) $value, DataType::TYPE_STRING);
                    }
                }
                $sheet->getStyle('B'.$row.':AG'.$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E9EFF7');
                $row++;
            }
            $lastContainer = strtoupper($container);

            $manifestGoods = collect($item->manifest_goods ?? []);
            $details = collect($item->perincian_barang ?? []);
            $lineCount = max(1, $manifestGoods->count(), $details->count());
            for ($line = 0; $line < $lineCount; $line++) {
                $good = $manifestGoods->get($line, []);
                $detail = $details->get($line, []);
                $shipper = $item->shipperJb;
                $values = [
                    'B' => $line === 0 ? $item->nomor_bl : null,
                    'C' => implode("\n", (array) (data_get($good, 'hs_codes') ?: (data_get($good, 'hs_code') ? [data_get($good, 'hs_code')] : []))),
                    'D' => ! $isLcl && $line === 0 ? $container : null,
                    'E' => ! $isLcl && $line === 0 ? $item->no_seal : null,
                    'F' => ! $isLcl && $line === 0 ? 1 : null,
                    'G' => ! $isLcl && $line === 0 ? 'Unit' : null,
                    'H' => ! $isLcl && $line === 0 ? trim($item->tipe_kontainer.' '.$item->size_kontainer).' stc :' : null,
                    'I' => data_get($good, 'jumlah'), 'J' => data_get($good, 'satuan'), 'K' => data_get($good, 'nama_barang'),
                    'L' => data_get($detail, 'jumlah'), 'M' => data_get($detail, 'satuan'), 'N' => data_get($detail, 'nama_barang'),
                    'O' => data_get($good, 'tonase'), 'P' => data_get($good, 'meter_kubik'),
                    'Q' => $shipper?->shipper ?: $item->pengirim,
                    'R' => $shipper?->alamat_shipper, 'S' => $shipper?->npwp_shipper,
                    'T' => $shipper?->consignee, 'U' => $shipper?->alamat_consignee, 'V' => $shipper?->npwp_consignee,
                    'W' => $shipper?->notify_party_consignee, 'X' => $shipper?->alamat_notify_party_consignee,
                    'Y' => $shipper?->npwp_notify_party_consignee, 'Z' => $shipper?->delivery_address_contact_person,
                    'AA' => $shipper?->document_ppftz_03, 'AB' => $shipper?->condition,
                    'AC' => $item->nomor_tanda_terima,
                    'AD' => $item->tanggal_tanda_terima?->format('d/m/Y'),
                    'AE' => ! $isLcl && $line === 0 ? 1 : null,
                    'AF' => ! $isLcl && $line === 0 ? 'Unit' : null,
                    'AG' => ! $isLcl && $line === 0 ? trim($item->tipe_kontainer.' '.$item->size_kontainer) : null,
                ];
                $values = array_replace($values, $item->sheet_values['details'][$line] ?? []);
                foreach ($values as $column => $value) {
                    if ($value !== null) {
                        if (in_array($column, ['I', 'L', 'O', 'P'], true) && $this->numericValue($value) !== null) {
                            $sheet->setCellValueExplicit($column.$row, $this->numericValue($value), DataType::TYPE_NUMERIC);
                        } else {
                            $sheet->setCellValueExplicit($column.$row, (string) $value, DataType::TYPE_STRING);
                        }
                    }
                }
                $row++;
            }
        }
        $sheet->freezePane('I8');
        $sheet->getStyle('B7:AG'.($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('B8:AG'.($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
        foreach (range('B', 'Z') as $column) {
            $sheet->getColumnDimension($column)->setWidth(in_array($column, ['K', 'N', 'R', 'U', 'X', 'Z']) ? 34 : 18);
        }
        foreach (['AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG'] as $column) {
            $sheet->getColumnDimension($column)->setWidth(20);
        }
        $path = tempnam(sys_get_temp_dir(), 'manifest_sugeng_');
        (new Xlsx($book))->save($path);
        $book->disconnectWorksheets();

        return $path;
    }

    private function numericValue(mixed $value): ?float
    {
        if (! is_scalar($value) || trim((string) $value) === '') {
            return null;
        }
        $text = trim((string) $value);
        if (str_contains($text, ',')) {
            $text = str_replace(',', '.', str_replace('.', '', $text));
        } elseif (preg_match('/^\d{1,3}(\.\d{3})+$/', $text)) {
            $text = str_replace('.', '', $text);
        }

        return is_numeric($text) ? (float) $text : null;
    }
}
