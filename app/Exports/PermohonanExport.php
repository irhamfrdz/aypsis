<?php

namespace App\Exports;

use App\Models\Permohonan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PermohonanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $permohonanIds;

    public function __construct(array $filters = [], array $permohonanIds = [])
    {
        $this->filters = $filters;
        $this->permohonanIds = $permohonanIds;
    }

    public function collection()
    {
        if (!empty($this->permohonanIds)) {
            $query = Permohonan::with(['supir', 'krani', 'kontainers'])->whereIn('id', $this->permohonanIds);
        } else {
            $query = Permohonan::with(['supir', 'krani', 'kontainers'])->latest();

            if (!empty($this->filters['search'])) {
                $searchTerm = $this->filters['search'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nomor_memo', 'like', "%{$searchTerm}%")
                      ->orWhere('kegiatan', 'like', "%{$searchTerm}%")
                      ->orWhere('vendor_perusahaan', 'like', "%{$searchTerm}%")
                      ->orWhere('dari', 'like', "%{$searchTerm}%")
                      ->orWhere('ke', 'like', "%{$searchTerm}%")
                      ->orWhere('catatan', 'like', "%{$searchTerm}%")
                      ->orWhere('alasan_adjustment', 'like', "%{$searchTerm}%")
                      ->orWhereHas('supir', function ($subq) use ($searchTerm) {
                          $subq->where('nama_panggilan', 'like', "%{$searchTerm}%")
                               ->orWhere('nama_lengkap', 'like', "%{$searchTerm}%");
                      })
                      ->orWhereHas('krani', function ($subq) use ($searchTerm) {
                          $subq->where('nama_panggilan', 'like', "%{$searchTerm}%")
                               ->orWhere('nama_lengkap', 'like', "%{$searchTerm}%");
                      });
                });
            }

            if (!empty($this->filters['date_from'])) {
                $query->whereDate('tanggal_memo', '>=', $this->filters['date_from']);
            }

            if (!empty($this->filters['date_to'])) {
                $query->whereDate('tanggal_memo', '<=', $this->filters['date_to']);
            }

            if (!empty($this->filters['kegiatan'])) {
                $query->where('kegiatan', $this->filters['kegiatan']);
            }

            if (!empty($this->filters['status'])) {
                $query->where('status', $this->filters['status']);
            }

            if (!empty($this->filters['amount_min'])) {
                $query->where('total_harga_setelah_adj', '>=', $this->filters['amount_min']);
            }
        }

        $rows = $query->orderBy('created_at', 'desc')->get()->map(function($r) {
            $kontainerList = '';
            try {
                $kontainerList = $r->kontainers->pluck('nomor_seri_gabungan')->filter()->unique()->values()->all();
                $kontainerList = implode('|', $kontainerList);
            } catch (\Exception $_) { $kontainerList = ''; }

            $kegiatanNama = null;
            try {
                if (!empty($r->kegiatan)) {
                    $mk = \App\Models\MasterKegiatan::where('kode_kegiatan', $r->kegiatan)->first();
                    $kegiatanNama = $mk ? $mk->nama_kegiatan : null;
                }
            } catch (\Exception $_) { $kegiatanNama = null; }

            return [
                'id' => $r->id,
                'nomor_memo' => $r->nomor_memo,
                'kegiatan' => $r->kegiatan,
                'kegiatan_nama' => $kegiatanNama,
                'supir_id' => $r->supir_id,
                'supir_nama' => optional($r->supir)->nama_panggilan,
                'krani_id' => $r->krani_id,
                'krani_nama' => optional($r->krani)->nama_panggilan,
                'vendor_perusahaan' => $r->vendor_perusahaan,
                'plat_nomor' => $r->plat_nomor,
                'no_chasis' => $r->no_chasis,
                'ukuran' => $r->ukuran,
                'tujuan' => $r->tujuan,
                'jumlah_kontainer' => $r->jumlah_kontainer,
                'jumlah_uang_jalan' => $r->jumlah_uang_jalan,
                'adjustment' => $r->adjustment,
                'alasan_adjustment' => $r->alasan_adjustment,
                'total_harga_setelah_adj' => $r->total_harga_setelah_adj,
                'catatan' => $r->catatan,
                'lampiran' => $r->lampiran,
                'status' => $r->status,
                'tanggal_memo' => $r->tanggal_memo,
                'created_at' => $r->created_at,
                'updated_at' => $r->updated_at,
                'kontainer_nomor_list' => $kontainerList,
            ];
        });

        return $rows;
    }

    public function headings(): array
    {
        return [
            'id','nomor_memo','kegiatan','kegiatan_nama','supir_id','supir_nama','krani_id','krani_nama',
            'vendor_perusahaan','plat_nomor','no_chasis','ukuran','tujuan','jumlah_kontainer','jumlah_uang_jalan',
            'adjustment','alasan_adjustment','total_harga_setelah_adj','catatan','lampiran','status','tanggal_memo',
            'created_at','updated_at','kontainer_nomor_list'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:X1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
