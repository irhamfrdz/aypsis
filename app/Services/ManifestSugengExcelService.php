<?php

namespace App\Services;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ManifestSugengExcelService
{
    public function create(Collection $items): string
    {
        $book = new Spreadsheet;
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('MANIFEST & PERINCIAN');
        $firstItem = $items->first();
        $sheet->setCellValue('B1', 'PT. ALEXINDO YAKIN PRIMA');
        $sheet->mergeCells('B1:AG1');
        $sheet->setCellValue('B2', 'Perusahaan Pelayaran Nasional');
        $sheet->mergeCells('B2:AG2');
        $sheet->setCellValue('B3', 'M A N I F E S T    O F    C A R G O');
        $sheet->mergeCells('B3:AG3');
        $sheet->setCellValue('B4', trim(($firstItem->nama_kapal ?? '').($firstItem->no_voyage ? ' / '.$firstItem->no_voyage : '')));
        $sheet->mergeCells('B4:AG4');
        $headers = [
            'A' => 'B/L NO.', 'B' => 'HS CODE', 'C' => 'MARK AND NUMBERS', 'D' => 'SEAL NO.',
            'E' => 'JUMLAH', 'F' => 'SATUAN', 'G' => 'DESCRIPTION OF GOODS', 'H' => 'JUMLAH',
            'I' => 'SATUAN', 'J' => 'DESCRIPTION OF GOODS', 'K' => 'WEIGHT', 'L' => 'MEASS',
            'M' => 'SHIPPER', 'N' => 'ADDRESS', 'O' => 'NO. IDENTITAS (NPWP SHIPPER)',
            'P' => 'CONSIGNEE', 'Q' => 'ADDRESS', 'R' => 'NO. IDENTITAS (NPWP CONSIGNEE)',
            'S' => 'NOTIFY PARTY (CONSIGNEE)', 'T' => 'ADDRESS', 'U' => 'NO. IDENTITAS (NPWP NOTIFY PARTY CONSIGNEE)',
            'V' => 'DELIVERY ADDRESS & CONTACT PERSON', 'W' => 'DOCUMENT PPFTZ-03',
            'X' => 'CONDITION', 'Y' => 'NUMBER', 'Z' => 'DATE', 'AA' => 'JUMLAH',
            'AB' => 'SATUAN', 'AC' => 'KETERANGAN PERHITUNGAN M3 / KGS',
        ];
        $headers = array_fill_keys(array_merge(range('B', 'Z'), ['AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG']), true);
        $sheet->setCellValue('I6', 'MANIFEST'); $sheet->mergeCells('I6:K6');
        $sheet->setCellValue('L6', 'PERINCIAN'); $sheet->mergeCells('L6:N6');
        $topHeaders = ['B' => 'B/L NO.', 'C' => 'HS CODE', 'D' => 'MARK AND NUMBERS', 'E' => 'SEAL NO.'];
        foreach ($topHeaders as $column => $label) {
            $sheet->setCellValue($column.'7', $label);
        }
        foreach (['B','C','D','E','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB'] as $column) $sheet->mergeCells($column.'7:'.$column.'8');
        $sheet->setCellValue('F7', 'DESCRIPTION OF GOODS'); $sheet->mergeCells('F7:H8');
        $sheet->setCellValue('I7', 'DESCRIPTION OF GOODS'); $sheet->mergeCells('I7:K7');
        $sheet->setCellValue('L7', 'DESCRIPTION OF GOODS'); $sheet->mergeCells('L7:N7');
        foreach (['I' => 'JUMLAH', 'J' => 'SATUAN', 'K' => 'DESCRIPTION OF GOODS', 'L' => 'JUMLAH', 'M' => 'SATUAN', 'N' => 'DESCRIPTION OF GOODS'] as $column => $label) {
            $sheet->setCellValue($column.'8', $label);
        }
        $sheet->setCellValue('AC7', 'RECEIPT SIGNS & ROAD LETTERS'); $sheet->mergeCells('AC7:AD7');
        $sheet->setCellValue('AC8', 'NUMBER'); $sheet->setCellValue('AD8', 'DATE');
        foreach (['AE' => 'JUMLAH', 'AF' => 'SATUAN', 'AG' => 'K E T E R A N G A N PERHITUNGAN M3 / KGS'] as $column => $label) {
            $sheet->setCellValue($column.'7', $label); $sheet->mergeCells($column.'7:'.$column.'8');
        }
        $sheet->getStyle('B1:AG4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B1:AG1')->getFont()->setBold(true)->setSize(22)->getColor()->setRGB('172554');
        $sheet->getStyle('B3:AG3')->getFont()->setBold(true)->setSize(18);
        $sheet->getStyle('B6:AG8')->getFont()->setBold(true);
        $sheet->getStyle('B6:AG8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getStyle('I6:K6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('92D050');
        $sheet->getStyle('L6:N6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('00B0F0');
        $sheet->getRowDimension(7)->setRowHeight(42); $sheet->getRowDimension(8)->setRowHeight(30);

        $row = 9;
        $lastContainer = null;
        $lastLclPartyKey = null;
        $lclPartyGroups = [];
        foreach ($items as $groupItem) {
            if ($groupItem->source_type !== 'lcl' && ! str_contains(strtoupper((string) $groupItem->tipe_kontainer), 'LCL')) {
                continue;
            }
            $groupContainer = implode('|', [$groupItem->nama_kapal, $groupItem->no_voyage, strtoupper(trim((string) $groupItem->nomor_kontainer))]);
            $groupShipper = $groupItem->shipperJb?->shipper ?: $groupItem->pengirim;
            $groupConsignee = $groupItem->shipperJb?->consignee;
            $lclPartyGroups[$groupContainer][mb_strtolower(trim((string) $groupShipper)).'|'.mb_strtolower(trim((string) $groupConsignee))] = true;
        }
        foreach ($items as $item) {
            $container = trim((string) $item->nomor_kontainer);
            $isLcl = $item->source_type === 'lcl' || str_contains(strtoupper((string) $item->tipe_kontainer), 'LCL');
            $isCargo = $item->source_type === 'cargo' || str_contains(strtoupper((string) $item->tipe_kontainer), 'CARGO');
            $containerKey = implode('|', [$item->nama_kapal, $item->no_voyage, strtoupper($container)]);
            $isNewContainer = strtoupper($container) !== $lastContainer;
            $mergeLclLayout = $isLcl && count($lclPartyGroups[$containerKey] ?? []) === 1;
            $fclDescription = trim('Container'.($item->size_kontainer ? ' '.$item->size_kontainer.' feet' : ''));
            $containerType = strtoupper(trim((string) $item->tipe_kontainer));
            if ($isLcl && ! $mergeLclLayout && $container !== '' && $isNewContainer) {
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
                $partyKey = implode('|', [
                    strtoupper($container),
                    mb_strtolower(trim((string) ($shipper?->shipper ?: $item->pengirim))),
                    mb_strtolower(trim((string) $shipper?->consignee)),
                ]);
                $showParty = ! $isLcl || ($line === 0 && $partyKey !== $lastLclPartyKey);
                if ($isLcl && $line === 0) {
                    $lastLclPartyKey = $partyKey;
                }
                $values = [
                    'B' => $line === 0 ? $item->nomor_bl : null,
                    'C' => implode("\n", (array) (data_get($good, 'hs_codes') ?: (data_get($good, 'hs_code') ? [data_get($good, 'hs_code')] : []))),
                    'D' => $line === 0 && (! $isLcl || ($mergeLclLayout && $isNewContainer)) ? $container : null,
                    'E' => $line === 0 && (! $isLcl || ($mergeLclLayout && $isNewContainer)) ? $item->no_seal : null,
                    'F' => $isCargo ? (data_get($good, 'jumlah') ?? data_get($detail, 'jumlah')) : ($line === 0 && (! $isLcl || ($mergeLclLayout && $isNewContainer)) ? 1 : null),
                    'G' => $isCargo ? (data_get($good, 'satuan') ?? data_get($detail, 'satuan')) : ($line === 0 && (! $isLcl || ($mergeLclLayout && $isNewContainer)) ? 'Unit' : null),
                    'H' => $isCargo ? null : ($line === 0 && (! $isLcl || ($mergeLclLayout && $isNewContainer)) ? ($containerType === 'FCL' ? $fclDescription : trim($item->tipe_kontainer.' '.$item->size_kontainer)).' stc :' : null),
                    'I' => data_get($good, 'jumlah'), 'J' => data_get($good, 'satuan'), 'K' => data_get($good, 'nama_barang'),
                    'L' => data_get($detail, 'jumlah'), 'M' => data_get($detail, 'satuan'), 'N' => data_get($detail, 'nama_barang'),
                    'O' => data_get($good, 'tonase'), 'P' => data_get($good, 'meter_kubik'),
                    'Q' => $showParty ? ($shipper?->shipper ?: $item->pengirim) : null,
                    'R' => $showParty ? $shipper?->alamat_shipper : null, 'S' => $showParty ? $shipper?->npwp_shipper : null,
                    'T' => $showParty ? $shipper?->consignee : null, 'U' => $showParty ? $shipper?->alamat_consignee : null, 'V' => $showParty ? $shipper?->npwp_consignee : null,
                    'W' => $showParty ? $shipper?->notify_party_consignee : null, 'X' => $showParty ? $shipper?->alamat_notify_party_consignee : null,
                    'Y' => $showParty ? $shipper?->npwp_notify_party_consignee : null, 'Z' => $showParty ? $shipper?->delivery_address_contact_person : null,
                    'AA' => $showParty ? $shipper?->document_ppftz_03 : null, 'AB' => $showParty ? $shipper?->condition : null,
                    'AC' => $item->nomor_tanda_terima,
                    'AD' => $item->tanggal_tanda_terima?->format('d/m/Y'),
                    'AE' => $isCargo ? (data_get($detail, 'jumlah') ?? data_get($good, 'jumlah')) : ($line === 0 && (! $isLcl || ($mergeLclLayout && $isNewContainer)) ? 1 : null),
                    'AF' => $isCargo ? (data_get($detail, 'satuan') ?? data_get($good, 'satuan')) : ($line === 0 && (! $isLcl || ($mergeLclLayout && $isNewContainer)) ? 'Unit' : null),
                    'AG' => $isCargo ? null : ($line === 0 && (! $isLcl || ($mergeLclLayout && $isNewContainer)) ? ($containerType === 'FCL' ? $fclDescription : trim($item->tipe_kontainer.' '.$item->size_kontainer)) : null),
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
        $sheet->freezePane('H9');
        $sheet->getStyle('B7:AG'.($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('B9:AG'.($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
        $widths = [75,75,105,95,55,65,180,65,65,807,65,65,807,110,110,190,210,160,190,210,160,190,210,160,230,130,120,150,110,65,55,220];
        foreach ($widths as $index => $width) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($index + 2))->setWidth(max(8, $width / 7));
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

    private function shiftColumn(string $column, int $offset): string
    {
        return Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($column) + $offset);
    }
}
