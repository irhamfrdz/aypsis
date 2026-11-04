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
        $sheet->removeRow(1, 2);
        $sheet->getSheetView()->setZoomScale(70);
        $sheet->freezePane('A7');

        $row = 8;
        foreach ($manifests as $manifest) {
            $this->fillRow($sheet, $row++, $manifest);

            if ($manifest->isFclBooking()) {
                foreach ($manifest->shipperDetails as $detail) {
                    $this->fillRow($sheet, $row++, $manifest, $detail);
                }
            }
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

    private function fillRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $row, Manifest $manifest, ?ManifestShipperDetail $detail = null): void
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
            'AG' => $manifest->tipe_kontainer ?: $manifest->size_kontainer,
        ];

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

        foreach (['F' => 1, 'I' => $quantity, 'L' => $perincianQuantity] as $column => $value) {
            if (is_numeric($value)) {
                $sheet->setCellValue($column.$row, (int) $value);
            }
        }
        foreach (['O' => $detail?->tonnage ?: $manifest->tonnage_perincian ?: $manifest->tonnage, 'P' => $detail?->volume ?: $manifest->volume_perincian ?: $manifest->volume] as $column => $value) {
            $numberValue = $this->indonesianNumber($value);
            if ($numberValue !== null) {
                $sheet->setCellValue($column.$row, $numberValue);
                $sheet->getStyle($column.$row)->getNumberFormat()->setFormatCode('#,##0.###');
            }
        }
        $sheet->getStyle('C'.$row)->getAlignment()->setWrapText(true);
        $sheet->getStyle('AA'.$row.':AB'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        if ($date) {
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
        if (str_contains($value, ',')) {
            $value = str_replace(',', '.', str_replace('.', '', $value));
        } elseif (preg_match('/^\d{1,3}(?:\.\d{3})+$/', $value)) {
            $value = str_replace('.', '', $value);
        }

        return is_numeric($value) ? (float) $value : null;
    }
}
