<?php

namespace App\Services;

use App\Models\Manifest;
use App\Models\ManifestShipperDetail;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ManifestExcelService
{
    public function create($manifests): string
    {
        $workbook = IOFactory::load(resource_path('templates/approval_tanda_terima_2.xlsx'));
        $sheet = $workbook->getSheet(0);
        $sheet->getSheetView()->setZoomScale(70);
        $sheet->freezePane('A9');

        $sorted = collect($manifests)->sort(function (Manifest $a, Manifest $b) {
            $rank = $this->exportRank($a) <=> $this->exportRank($b);
            if ($rank !== 0) {
                return $rank;
            }

            return (($a->nomor_urut === null) <=> ($b->nomor_urut === null))
                ?: ((int) $a->nomor_urut <=> (int) $b->nomor_urut)
                ?: ($a->id <=> $b->id);
        })->values();
        $lclGroups = $sorted->filter(fn (Manifest $manifest) => $this->exportRank($manifest) === 0)
            ->groupBy(fn (Manifest $manifest) => $this->lclGroupKey($manifest));
        $writtenLcl = [];
        $row = 9;
        foreach ($sorted as $manifest) {
            if ($this->exportRank($manifest) === 0) {
                $key = $this->lclGroupKey($manifest);
                if (isset($writtenLcl[$key])) {
                    continue;
                }
                $writtenLcl[$key] = true;
                $this->fillLclContainerRow($sheet, $row++, $manifest);
                foreach ($lclGroups[$key] as $item) {
                    $this->fillRow($sheet, $row++, $item, null, 'lcl_item');
                }
                continue;
            }

            if ($manifest->isFclBooking()) {
                $hasDetails = $manifest->shipperDetails->isNotEmpty();
                $this->fillRow($sheet, $row++, $manifest, null, $hasDetails ? 'booking_parent' : 'standard');
                foreach ($manifest->shipperDetails as $detail) {
                    $this->fillRow($sheet, $row++, $manifest, $detail, 'booking_detail');
                }
                continue;
            }

            $this->fillRow($sheet, $row++, $manifest);
        }

        while ($workbook->getSheetCount() > 1) {
            $workbook->removeSheetByIndex(1);
        }

        $path = tempnam(sys_get_temp_dir(), 'manifest_');
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

    private function exportRank(Manifest $manifest): int
    {
        $type = strtoupper(trim((string) $manifest->tipe_kontainer));
        $size = strtoupper(trim((string) $manifest->size_kontainer));

        if (str_contains($type, 'LCL') || str_contains($size, 'LCL')) {
            return 0;
        }
        if ($manifest->isFclBooking()) {
            return 1;
        }
        if (str_contains($type, 'CARGO') || str_contains($size, 'CARGO')) {
            return 3;
        }

        return 2;
    }

    private function lclGroupKey(Manifest $manifest): string
    {
        $number = strtoupper(trim((string) $manifest->nomor_kontainer));

        return $number !== '' ? $number : 'manifest:'.$manifest->id;
    }

    private function fillLclContainerRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $row, Manifest $manifest): void
    {
        foreach (['D' => $manifest->nomor_kontainer, 'E' => $manifest->no_seal,
            'G' => 'Unit', 'H' => $this->containerDescription($manifest),
            'AF' => 'Unit', 'AG' => $this->containerLabel($manifest).' / LCL'] as $column => $value) {
            if (filled($value)) {
                $sheet->setCellValueExplicit($column.$row, (string) $value, DataType::TYPE_STRING);
            }
        }
        $sheet->setCellValue('F'.$row, 1);
        $sheet->setCellValue('AE'.$row, 1);
    }

    private function containerLabel(Manifest $manifest): string
    {
        return str_replace(' stc :', '', $this->containerDescription($manifest));
    }

    private function fillRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $row, Manifest $manifest, ?ManifestShipperDetail $detail = null, string $layout = 'standard'): void
    {
        $shipper = $detail?->shipperConsignee ?? $manifest->shipperJb ?? $manifest->shipperConsignee;
        $sender = $detail?->pengirim ?: $manifest->pengirim;
        $receiver = $detail?->penerima ?: $manifest->penerima;
        $notifyParty = $detail?->notify_party ?: $manifest->notify_party;
        $itemName = $detail?->nama_barang ?: $manifest->nama_barang;
        $quantity = $detail?->kuantitas ?? $manifest->kuantitas;
        $unit = $detail?->satuan ?: $manifest->satuan;
        $perincian = $detail ? null : data_get($manifest->perincian_barang, '0');
        $perincianName = data_get($perincian, 'nama_barang') ?: $itemName;
        $perincianQuantity = data_get($perincian, 'jumlah') ?? $quantity;
        $perincianUnit = data_get($perincian, 'satuan') ?: $unit;
        $date = $detail?->penerimaan ?: $manifest->tanggal_berangkat ?: $manifest->penerimaan;
        $number = $detail?->nomor_tanda_terima ?: $manifest->nomor_tanda_terima_display;
        $shipperValues = [
            'shipper' => $shipper?->shipper ?: $sender,
            'alamat_shipper' => $shipper?->alamat_shipper ?: ($detail?->alamat_pengirim ?: $manifest->alamat_pengirim),
            'npwp_shipper' => $shipper?->npwp_shipper,
            'consignee' => $shipper?->consignee ?: $receiver,
            'alamat_consignee' => $shipper?->alamat_consignee ?: ($detail?->alamat_penerima ?: $manifest->alamat_penerima),
            'npwp_consignee' => $shipper?->npwp_consignee,
            'notify_party_consignee' => $shipper?->notify_party_consignee ?: $notifyParty,
            'alamat_notify_party_consignee' => $shipper?->alamat_notify_party_consignee ?: ($detail?->alamat_notify_party ?: $manifest->alamat_notify_party),
            'npwp_notify_party_consignee' => $shipper?->npwp_notify_party_consignee,
            'delivery_address_contact_person' => $detail?->alamat_pengiriman ?: $manifest->alamat_pengiriman,
            'document_ppftz_03' => $shipper?->document_ppftz_03,
            'condition' => $shipper?->condition,
        ];

        $values = [
            'B' => $manifest->nomor_bl,
            'C' => $detail?->hs_code ?: $manifest->hs_code,
            'D' => $manifest->nomor_kontainer,
            'E' => $manifest->no_seal,
            'G' => 'Unit',
            'H' => $this->containerDescription($manifest),
            'J' => $unit,
            'K' => $itemName,
            'M' => $perincianUnit,
            'N' => $perincianName,
            'Q' => $shipperValues['shipper'],
            'R' => $shipperValues['alamat_shipper'],
            'S' => $shipperValues['npwp_shipper'],
            'T' => $shipperValues['consignee'],
            'U' => $shipperValues['alamat_consignee'],
            'V' => $shipperValues['npwp_consignee'],
            'W' => $shipperValues['notify_party_consignee'],
            'X' => $shipperValues['alamat_notify_party_consignee'],
            'Y' => $shipperValues['npwp_notify_party_consignee'],
            'Z' => $shipperValues['delivery_address_contact_person'],
            'AA' => $shipperValues['document_ppftz_03'],
            'AB' => $shipperValues['condition'],
            'AC' => $number,
            'AG' => $layout === 'booking_parent'
                ? $this->containerLabel($manifest).' / FCL +'
                : ($manifest->tipe_kontainer ?: $manifest->size_kontainer),
        ];

        if ($layout === 'booking_parent' || ($layout === 'standard' && $this->exportRank($manifest) === 2)) {
            $values['AF'] = 'Unit';
        }

        if ($layout === 'lcl_item') {
            foreach (['D', 'E', 'G', 'H', 'AG'] as $column) {
                unset($values[$column]);
            }
        } elseif ($layout === 'booking_parent') {
            foreach (['M', 'N', 'AC'] as $column) {
                unset($values[$column]);
            }
        } elseif ($layout === 'booking_detail') {
            foreach (['C', 'D', 'E', 'G', 'H', 'J', 'K', 'AG'] as $column) {
                unset($values[$column]);
            }
        }

        foreach ($values as $column => $value) {
            if (filled($value)) {
                $sheet->setCellValueExplicit($column.$row, (string) $value, DataType::TYPE_STRING);
            }
        }

        $documentType = strtoupper(trim(preg_replace('/[\s_-]+/', ' ', (string) $shipperValues['document_ppftz_03'])));
        foreach (['Q', 'T'] as $column) {
            $fill = $sheet->getStyle($column.$row)->getFill();
            if ($documentType === 'NON FAKTUR') {
                $fill->setFillType(Fill::FILL_NONE);
            } else {
                $fill->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            }
        }
        if ($documentType === 'AYP') {
            $sheet->getStyle('AA'.$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF0000');
        }

        $numbers = ['F' => 1, 'I' => $quantity, 'L' => $perincianQuantity];
        if ($layout === 'lcl_item') {
            unset($numbers['F']);
        } elseif ($layout === 'booking_parent') {
            unset($numbers['L']);
        } elseif ($layout === 'booking_detail') {
            unset($numbers['F'], $numbers['I']);
        }
        foreach ($numbers as $column => $value) {
            if (is_numeric($value)) {
                $sheet->setCellValue($column.$row, (int) $value);
            }
        }
        if ($layout === 'booking_parent' || ($layout === 'standard' && $this->exportRank($manifest) === 2)) {
            $sheet->setCellValue('AE'.$row, 1);
        }
        foreach ($layout === 'booking_detail' ? [] : ['O' => $detail?->tonnage ?: $manifest->tonnage_perincian ?: $manifest->tonnage, 'P' => $detail?->volume ?: $manifest->volume_perincian ?: $manifest->volume] as $column => $value) {
            $numberValue = $this->indonesianNumber($value);
            if ($numberValue !== null) {
                $sheet->setCellValue($column.$row, $numberValue);
                $sheet->getStyle($column.$row)->getNumberFormat()->setFormatCode('#,##0.###');
            }
        }
        $sheet->getStyle('C'.$row)->getAlignment()->setWrapText(true);
        $sheet->getStyle('AA'.$row.':AB'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        if ($date && $layout !== 'booking_parent') {
            $sheet->setCellValue('AD'.$row, ExcelDate::PHPToExcel(Carbon::parse($date)));
        }
    }

    private function containerDescription(Manifest $manifest): string
    {
        $size = $manifest->size_kontainer ?: $manifest->tipe_kontainer;
        if (preg_match('/\b(20|40|45)\s*(?:ft|feet|hc)?\b/i', (string) $size, $matches)) {
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
        if (is_numeric($value)) {
            return (float) $value;
        }
        if (str_contains($value, ',')) {
            $value = str_replace(',', '.', str_replace('.', '', $value));
        } elseif (preg_match('/^\d{1,3}(?:\.\d{3})+$/', $value)) {
            $value = str_replace('.', '', $value);
        }

        return is_numeric($value) ? (float) $value : null;
    }
}
