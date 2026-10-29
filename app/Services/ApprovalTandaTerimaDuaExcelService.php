<?php

namespace App\Services;

use App\Models\ApprovalTandaTerimaDuaGoods;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ApprovalTandaTerimaDuaExcelService
{
    public function create(string $sourceType, Model $receipt, ?ApprovalTandaTerimaDuaGoods $override = null): string
    {
        return $this->createMany($sourceType, [['receipt' => $receipt, 'override' => $override]]);
    }

    public function createMany(string $sourceType, array $entries): string
    {
        $template = resource_path('templates/approval_tanda_terima_2.xlsx');
        $workbook = IOFactory::load($template);
        $sheet = $workbook->getSheet(0);
        foreach ($entries as $index => $entry) {
            $this->fillRow($sheet, 9 + $index, $sourceType, $entry['receipt'], $entry['override'] ?? null);
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

    private function fillRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $row, string $sourceType, Model $receipt, ?ApprovalTandaTerimaDuaGoods $override): void
    {
        $sourceGood = $this->sourceGood($sourceType, $receipt);
        $manifestGood = $override?->manifest_goods[0] ?? $sourceGood;
        $perincianGood = $override?->goods[0] ?? $sourceGood;
        $shipper = $receipt->shipperJb;
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
            'C' => data_get($manifestGood, 'hs_code'),
            'D' => $container,
            'E' => $seal,
            'G' => 'Unit',
            'H' => $this->containerDescription($sourceType, $receipt),
            'J' => data_get($manifestGood, 'satuan'),
            'K' => data_get($manifestGood, 'nama_barang'),
            'M' => data_get($perincianGood, 'satuan'),
            'N' => data_get($perincianGood, 'nama_barang'),
            'Q' => $shipper?->shipper ?: $sender,
            'R' => $shipper?->alamat_shipper ?: $receipt->alamat_pengirim,
            'S' => $shipper?->npwp_shipper,
            'T' => $shipper?->consignee ?: $receiver,
            'U' => $shipper?->alamat_consignee ?: $receipt->alamat_penerima,
            'V' => $shipper?->npwp_consignee,
            'W' => $shipper?->notify_party_consignee ?: $receipt->notify_party,
            'X' => $shipper?->alamat_notify_party_consignee ?: $receipt->alamat_notify_party,
            'Y' => $shipper?->npwp_notify_party_consignee,
            'Z' => $shipper?->delivery_address_contact_person,
            'AA' => $shipper?->document_ppftz_03,
            'AB' => $shipper?->condition,
            'AC' => $number,
            'AG' => $receipt->tipe_kontainer ?: ($receipt->size_kontainer ?: $receipt->size),
        ];
        foreach ($textCells as $column => $value) {
            if (filled($value)) {
                $sheet->setCellValueExplicit($column.$row, (string) $value, DataType::TYPE_STRING);
            }
        }

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
