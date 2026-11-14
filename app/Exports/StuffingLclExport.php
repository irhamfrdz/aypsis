<?php

namespace App\Exports;

use App\Models\TandaTerimaLclKontainerPivot;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StuffingLclExport extends DefaultValueBinder implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithStyles, WithCustomValueBinder
{
    public function __construct(private string $sealStatus = '', private ?int $locationId = null) {}

    public function query()
    {
        return TandaTerimaLclKontainerPivot::query()
            ->with(['tandaTerima.items', 'tandaTerima.tujuanPengiriman'])
            ->when($this->sealStatus === 'sealed', fn ($query) => $query->whereNotNull('nomor_seal')->where('nomor_seal', '<>', ''))
            ->when($this->sealStatus === 'unsealed', function ($query) {
                $table = 'tanda_terima_lcl_kontainer_pivot';
                $query->where(fn ($q) => $q->whereNull('nomor_seal')->orWhere('nomor_seal', ''))
                    ->whereNotExists(function ($q) use ($table) {
                        $q->selectRaw('1')->from('prospek')
                            ->whereColumn('prospek.nomor_kontainer', $table.'.nomor_kontainer')
                            ->whereRaw("COALESCE(prospek.no_seal, '') = COALESCE({$table}.nomor_seal, '')")
                            ->where('prospek.status', 'sudah_muat');
                    })
                    // A legacy batch is shipped only when all of its rows are in the historical period.
                    ->whereExists(function ($q) use ($table) {
                        $q->selectRaw('1')->from($table.' as batch')
                            ->whereColumn('batch.nomor_kontainer', $table.'.nomor_kontainer')
                            ->whereRaw("COALESCE(batch.nomor_seal, '') = COALESCE({$table}.nomor_seal, '')")
                            ->where(function ($dates) {
                                $dates->whereRaw('COALESCE(batch.assigned_at, batch.created_at) IS NULL')
                                    ->orWhereRaw('COALESCE(batch.assigned_at, batch.created_at) < ?', ['2025-01-01 00:00:00'])
                                    ->orWhereRaw('COALESCE(batch.assigned_at, batch.created_at) >= ?', ['2026-07-01 00:00:00']);
                            });
                    });
            })
            ->when($this->locationId, fn ($query) => $query->whereHas('tandaTerima', fn ($q) => $q->where('tujuan_pengiriman_id', $this->locationId)))
            ->orderByDesc('assigned_at')->orderByDesc('id');
    }

    public function headings(): array
    {
        return ['Nomor Kontainer', 'Size', 'Tipe', 'Lokasi', 'Status Seal', 'Nomor Seal', 'Tanggal Seal', 'Tanggal Stuffing', 'Nomor Tanda Terima', 'Tanggal Tanda Terima', 'Pengirim', 'Penerima', 'Volume (m³)', 'Berat (ton)'];
    }

    public function map($row): array
    {
        $receipt = $row->tandaTerima;

        return [$row->nomor_kontainer, $row->size_kontainer, $row->tipe_kontainer,
            $receipt?->tujuanPengiriman?->nama_tujuan, filled($row->nomor_seal) ? 'Sudah Diseal' : 'Belum Diseal',
            $row->nomor_seal, $row->tanggal_seal?->format('d/m/Y'), $row->assigned_at?->format('d/m/Y H:i'),
            $receipt?->nomor_tanda_terima, $receipt?->tanggal_tanda_terima?->format('d/m/Y'),
            $receipt?->nama_pengirim, $receipt?->nama_penerima,
            (float) ($receipt?->items->sum('meter_kubik') ?? 0), (float) ($receipt?->items->sum('tonase') ?? 0)];
    }

    public function bindValue(Cell $cell, $value): bool
    {
        if (is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->freezePane('A2');
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        return [1 => ['font' => ['bold' => true]]];
    }
}
