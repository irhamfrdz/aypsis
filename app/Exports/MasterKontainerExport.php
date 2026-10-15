<?php

namespace App\Exports;

use App\Models\Kontainer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MasterKontainerExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(private readonly array $filters = []) {}

    public function collection(): Collection
    {
        $query = Kontainer::query();

        if (! empty($this->filters['search'])) {
            $query->where('nomor_seri_gabungan', 'like', '%'.$this->filters['search'].'%');
        }

        foreach (['vendor', 'ukuran', 'status'] as $field) {
            if (! empty($this->filters[$field])) {
                $query->where($field, $this->filters[$field]);
            }
        }

        switch ($this->filters['tanggal_sewa'] ?? null) {
            case 'tanpa_tanggal_akhir':
                $query->whereNotNull('tanggal_mulai_sewa')->whereNull('tanggal_selesai_sewa');
                break;
            case 'ada_tanggal_akhir':
                $query->whereNotNull('tanggal_selesai_sewa');
                break;
            case 'tanpa_tanggal_mulai':
                $query->whereNull('tanggal_mulai_sewa');
                break;
            case 'lengkap':
                $query->whereNotNull('tanggal_mulai_sewa')->whereNotNull('tanggal_selesai_sewa');
                break;
        }

        return $query->orderBy('nomor_seri_gabungan')->get()->values()->map(function (Kontainer $kontainer, int $index) {
            $status = in_array($kontainer->status, ['Disewa', 'Digunakan', 'rented'])
                ? 'Disewa'
                : ($kontainer->status === 'inactive' ? 'Nonaktif' : $kontainer->status);

            return [
                $index + 1,
                $kontainer->nomor_seri_gabungan ?? '-',
                $kontainer->awalan_kontainer ?? '-',
                $kontainer->nomor_seri ?? '-',
                $kontainer->akhiran ?? '-',
                $kontainer->ukuran ?? '-',
                $kontainer->tipe_kontainer ?? '-',
                $kontainer->vendor ?? '-',
                $status,
                $kontainer->tanggal_mulai_sewa?->format('d/M/Y') ?? '-',
                $kontainer->tanggal_selesai_sewa?->format('d/M/Y') ?? '-',
                $kontainer->created_at?->format('d-m-Y H:i:s') ?? '-',
                $kontainer->updated_at?->format('d-m-Y H:i:s') ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No', 'Nomor Kontainer', 'Awalan', 'Nomor Seri', 'Akhiran', 'Ukuran', 'Tipe',
            'Vendor', 'Status', 'Tanggal Mulai Sewa', 'Tanggal Selesai Sewa', 'Tanggal Dibuat', 'Tanggal Diperbarui',
        ];
    }
}
