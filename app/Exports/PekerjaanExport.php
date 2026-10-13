<?php

namespace App\Exports;

use App\Models\Pekerjaan;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PekerjaanExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(
        private readonly ?string $search = null,
        private readonly ?string $status = null,
    ) {}

    public function query(): Builder
    {
        return Pekerjaan::query()
            ->when($this->search !== null && $this->search !== '', fn (Builder $query) => $query->search($this->search))
            ->when($this->status !== null && $this->status !== '', fn (Builder $query) => $query->where('is_active', $this->status === 'active'))
            ->orderBy('nama_pekerjaan');
    }

    public function headings(): array
    {
        return ['ID', 'Nama Pekerjaan', 'Kode Pekerjaan', 'Divisi', 'Deskripsi', 'Status', 'Dibuat', 'Diperbarui'];
    }

    public function map($pekerjaan): array
    {
        return [
            $pekerjaan->id,
            $pekerjaan->nama_pekerjaan,
            $pekerjaan->kode_pekerjaan,
            $pekerjaan->divisi,
            $pekerjaan->deskripsi,
            $pekerjaan->is_active ? 'Aktif' : 'Tidak Aktif',
            $pekerjaan->created_at?->format('Y-m-d H:i:s'),
            $pekerjaan->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
