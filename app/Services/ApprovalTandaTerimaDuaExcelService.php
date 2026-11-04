<?php

namespace App\Services;

use App\Models\ApprovalTandaTerimaDuaGoods;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ApprovalTandaTerimaDuaExcelService
{
    public function create(string $sourceType, Model $receipt, ?ApprovalTandaTerimaDuaGoods $override = null, ?array $shipperOverride = null): string
    {
        return $this->createMany($sourceType, [['receipt' => $receipt, 'override' => $override, 'shipper_override' => $shipperOverride]]);
    }

    public function createMany(string $sourceType, array $entries): string
    {
        $template = resource_path('templates/approval_tanda_terima_2.xlsx');
        $workbook = IOFactory::load($template);
        $sheet = $workbook->getSheet(0);
        $sheet->removeRow(1, 2);
        $sheet->getSheetView()->setZoomScale(70);
        $sheet->freezePane('A7');
        $row = 8;
        foreach ($entries as $entry) {
            $this->fillRow($sheet, $row++, $sourceType, $entry['receipt'], $entry['override'] ?? null, null, $entry['shipper_override'] ?? null);
            if ($sourceType === 'fcl' && $entry['receipt']->relationLoaded('fclShipperDetails')) {
                foreach ($entry['receipt']->fclShipperDetails as $detail) {
                    $detailContext = 'detail:'.$detail->id;
                    $detailOverride = $entry['detail_shipper_overrides'][$detailContext] ?? null;
                    $this->fillRow($sheet, $row++, $sourceType, $entry['receipt'], null, $detail, $detailOverride);
                }
            }
        }
        while ($workbook->getSheetCount() > 1) {
            $workbook->removeSheetByIndex(1);
        }

        $path = tempnam(sys_get_temp_dir(), 'approval_tt2_');
        try {
            (new Xlsx($workbook))->save($path);
        } catch (\Throwable $exception) {
            @unlink($path);
            throw $exception;
        } finally {
            $workbook->disconnectWorksheets();
        }

        return $path;
    }

    private function fillRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $row, string $sourceType, Model $receipt, ?ApprovalTandaTerimaDuaGoods $override, ?\App\Models\ApprovalTandaTerimaDuaFclShipper $detail = null, ?array $shipperOverride = null): void
    {
        $sourceGood = $this->sourceGood($sourceType, $receipt);
        $manifestGood = $detail?->manifest_goods ?? $override?->manifest_goods[0] ?? $sourceGood;
        $perincianGood = $detail?->goods ?? $override?->goods[0] ?? $sourceGood;
        $hsCodes = data_get($manifestGood, 'hs_codes');
        $hsCodes = is_array($hsCodes) ? $hsCodes : (filled(data_get($manifestGood, 'hs_code')) ? [data_get($manifestGood, 'hs_code')] : []);
        $shipper = $detail?->shipperJb ?? $receipt->shipperJb;
        $shipperValues = array_replace($shipper?->getAttributes() ?? [], $shipperOverride ?? []);
        $number = match ($sourceType) {
            'fcl' => $receipt->no_surat_jalan,
            'lcl' => $receipt->nomor_tanda_terima,
            default => $receipt->no_tanda_terima ?: $receipt->nomor_tanda_terima,
        };
        $date = $sourceType === 'fcl' ? ($receipt->tanggal ?: $receipt->tanggal_surat_jalan) : $receipt->tanggal_tanda_terima;
        $container = $sourceType === 'lcl' ? $receipt->nomor_kontainer : $receipt->no_kontainer;
        $seal = $sourceType === 'lcl' ? $receipt->nomor_seal : $receipt->no_seal;
        $sender = $sourceType === 'lcl' ? $receipt->nama_pengirim : $receipt->pengirim;
        $receiver = $sourceType === 'lcl' ? $receipt->nama_penerima : $receipt->penerima;

        // Sheet 1 has container details (F:H), manifest goods (I:K), and perincian goods (L:N).
        $textCells = [
            'C' => implode("\n", $hsCodes),
            'D' => $container,
            'E' => $seal,
            'G' => 'Unit',
            'H' => $this->containerDescription($sourceType, $receipt),
            'J' => data_get($manifestGood, 'satuan'),
            'K' => data_get($manifestGood, 'nama_barang'),
            'M' => data_get($perincianGood, 'satuan'),
            'N' => data_get($perincianGood, 'nama_barang'),
            'Q' => ($shipperValues['shipper'] ?? null) ?: $sender,
            'R' => ($shipperValues['alamat_shipper'] ?? null) ?: $receipt->alamat_pengirim,
            'S' => $shipperValues['npwp_shipper'] ?? null,
            'T' => ($shipperValues['consignee'] ?? null) ?: $receiver,
            'U' => ($shipperValues['alamat_consignee'] ?? null) ?: $receipt->alamat_penerima,
            'V' => $shipperValues['npwp_consignee'] ?? null,
            'W' => ($shipperValues['notify_party_consignee'] ?? null) ?: $receipt->notify_party,
            'X' => ($shipperValues['alamat_notify_party_consignee'] ?? null) ?: $receipt->alamat_notify_party,
            'Y' => $shipperValues['npwp_notify_party_consignee'] ?? null,
            'Z' => $shipperValues['delivery_address_contact_person'] ?? null,
            'AA' => $shipperValues['document_ppftz_03'] ?? null,
            'AB' => $shipperValues['condition'] ?? null,
            'AC' => $number,
            'AG' => $receipt->tipe_kontainer ?: ($receipt->size_kontainer ?: $receipt->size),
        ];
        foreach ($textCells as $column => $value) {
            if (filled($value)) {
                $sheet->setCellValueExplicit($column.$row, (string) $value, DataType::TYPE_STRING);
            }
        }
        $documentType = strtoupper(trim(preg_replace('/[\s_-]+/', ' ', (string) ($shipperValues['document_ppftz_03'] ?? ''))));
        foreach (['Q', 'T'] as $column) {
            $fill = $sheet->getStyle($column.$row)->getFill();
            if ($documentType === 'NON FAKTUR') {
                $fill->setFillType(Fill::FILL_NONE);
            } else {
                $fill->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFFF00');
            }
        }
        if ($documentType === 'AYP') {
            $sheet->getStyle('AA'.$row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFFF0000');
        }
        $sheet->getStyle('C'.$row)->getAlignment()->setWrapText(true);
        $sheet->getStyle('AA'.$row.':AB'.$row)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        foreach (['F' => 1, 'I' => data_get($manifestGood, 'jumlah'), 'L' => data_get($perincianGood, 'jumlah')] as $column => $value) {
            if (is_numeric($value)) {
                $sheet->setCellValue($column.$row, (int) $value);
            }
        }
        foreach (['O' => data_get($manifestGood, 'tonase'), 'P' => data_get($manifestGood, 'meter_kubik')] as $column => $input) {
            $number = $this->indonesianNumber($input);
            if ($number !== null) {
                $sheet->setCellValue($column.$row, $number);
                $sheet->getStyle($column.$row)->getNumberFormat()->setFormatCode($this->numberFormatForInput($input));
            }
        }
        if ($date) {
            $sheet->setCellValue('AD'.$row, ExcelDate::PHPToExcel(Carbon::parse($date)));
        }
    }

    private function sourceGood(string $sourceType, Model $receipt): array
    {
        $item = match ($sourceType) {
            'fcl' => collect($receipt->dimensi_items ?: $receipt->dimensi_details ?: $receipt->nama_barang ?: [])->first(),
            'lcl' => $receipt->items->first(),
            default => $receipt->dimensiItems->first(),
        };
        if (is_string($item)) {
            return ['nama_barang' => $item];
        }
        if ($item) {
            return is_array($item) ? $item : $item->toArray();
        }

        return [
            'nama_barang' => $receipt->jenis_barang ?: ($sourceType === 'ttsj' ? $receipt->nama_barang : null),
            'jumlah' => $receipt->jumlah_barang ?: $receipt->jumlah,
            'satuan' => $receipt->satuan_barang ?: $receipt->satuan,
            'tonase' => $receipt->tonase,
            'meter_kubik' => $receipt->meter_kubik,
        ];
    }

    private function containerDescription(string $sourceType, Model $receipt): string
    {
        $size = $receipt->size_kontainer ?: $receipt->size;
        if ($sourceType === 'lcl' && blank($size)) {
            $pivot = $receipt->kontainerPivot->firstWhere('nomor_kontainer', $receipt->nomor_kontainer)
                ?? $receipt->kontainerPivot->first();
            $size = $pivot?->size_kontainer;
        }
        if (blank($size)) {
            $size = $receipt->tipe_kontainer;
        }
        if (is_scalar($size) && preg_match('/\b(20|40|45)\s*(?:ft|feet|hc)?\b/i', (string) $size, $matches)) {
            $highCube = preg_match('/\b40\s*hc\b/i', (string) $size) ? ' HC' : '';

            return "Container {$matches[1]} feet{$highCube} stc :";
        }

        return 'Container stc :';
    }

    private function indonesianNumber(mixed $value): ?float
    {
        if (! is_numeric($value) && ! is_string($value)) {
            return null;
        }
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        if (str_contains($value, ',')) {
            $value = str_replace(',', '.', str_replace('.', '', $value));
        } elseif (preg_match('/^\d{1,3}(?:\.\d{3})+$/', $value)) {
            $value = str_replace('.', '', $value);
        }

        return is_numeric($value) ? (float) $value : null;
    }

    private function numberFormatForInput(mixed $value): string
    {
        $value = trim((string) $value);
        if (str_contains($value, ',')) {
            $decimals = strlen(substr(strrchr($value, ','), 1));
        } elseif (preg_match('/^\d+\.(\d+)$/', $value, $matches)
            && ! preg_match('/^\d{1,3}(?:\.\d{3})+$/', $value)) {
            $decimals = strlen($matches[1]);
        } else {
            $decimals = 0;
        }

        return '#,##0'.($decimals > 0 ? '.'.str_repeat('0', $decimals) : '');
    }
}
