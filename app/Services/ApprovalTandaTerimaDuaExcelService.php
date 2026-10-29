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
        $template = resource_path('templates/approval_tanda_terima_2.xlsx');
        $workbook = IOFactory::load($template);
        $sheet = $workbook->getSheet(0);
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
            'C9' => data_get($manifestGood, 'hs_code'),
            'D9' => $container,
            'E9' => $seal,
            'G9' => 'Unit',
            'H9' => $this->containerDescription($sourceType, $receipt),
            'J9' => data_get($manifestGood, 'satuan'),
            'K9' => data_get($manifestGood, 'nama_barang'),
            'M9' => data_get($perincianGood, 'satuan'),
            'N9' => data_get($perincianGood, 'nama_barang'),
            'Q9' => $shipper?->shipper ?: $sender,
            'R9' => $shipper?->alamat_shipper ?: $receipt->alamat_pengirim,
            'S9' => $shipper?->npwp_shipper,
            'T9' => $shipper?->consignee ?: $receiver,
            'U9' => $shipper?->alamat_consignee ?: $receipt->alamat_penerima,
            'V9' => $shipper?->npwp_consignee,
            'W9' => $shipper?->notify_party_consignee ?: $receipt->notify_party,
            'X9' => $shipper?->alamat_notify_party_consignee ?: $receipt->alamat_notify_party,
            'Y9' => $shipper?->npwp_notify_party_consignee,
            'Z9' => $shipper?->delivery_address_contact_person,
            'AA9' => $shipper?->document_ppftz_03,
            'AB9' => $shipper?->condition,
            'AC9' => $number,
            'AG9' => $receipt->tipe_kontainer ?: ($receipt->size_kontainer ?: $receipt->size),
        ];
        foreach ($textCells as $cell => $value) {
            if (filled($value)) {
                $sheet->setCellValueExplicit($cell, (string) $value, DataType::TYPE_STRING);
            }
        }

        foreach (['F9' => 1, 'I9' => data_get($manifestGood, 'jumlah'), 'L9' => data_get($perincianGood, 'jumlah')] as $cell => $value) {
            if (is_numeric($value)) {
                $sheet->setCellValue($cell, (int) $value);
            }
        }
        $weight = $this->indonesianNumber(data_get($manifestGood, 'tonase'));
        $measure = $this->indonesianNumber(data_get($manifestGood, 'meter_kubik'));
        if ($weight !== null) {
            $sheet->setCellValue('O9', $weight);
        }
        if ($measure !== null) {
            $sheet->setCellValue('P9', $measure);
        }
        if ($date) {
            $sheet->setCellValue('AD9', ExcelDate::PHPToExcel(Carbon::parse($date)));
        }
        if ($measure !== null) {
            $sheet->setCellValue('AE9', $measure);
            $sheet->setCellValueExplicit('AF9', 'M3', DataType::TYPE_STRING);
        } elseif ($weight !== null) {
            $sheet->setCellValue('AE9', $weight);
            $sheet->setCellValueExplicit('AF9', 'KGS', DataType::TYPE_STRING);
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
}
