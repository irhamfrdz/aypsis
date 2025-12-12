<?php

namespace App\Exports;

use App\Models\TandaTerima;
use App\Models\SuratJalan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\MasterKegiatan;

class TandaTerimaFilteredExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $mode;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->mode = $filters['mode'] ?? '';
    }

    public function collection()
    {
        // If mode is 'missing', export SuratJalan rows matching filters
        if ($this->mode === 'missing') {
            $query = SuratJalan::query();

            if (!empty($this->filters['search'])) {
                $search = $this->filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('no_surat_jalan', 'like', "%{$search}%")
                      ->orWhere('supir', 'like', "%{$search}%")
                      ->orWhere('no_kontainer', 'like', "%{$search}%")
                      ->orWhere('no_plat', 'like', "%{$search}%");
                });
            }

            if (!empty($this->filters['status'])) {
                $query->where('status', $this->filters['status']);
            }

            if (!empty($this->filters['kegiatan'])) {
                $query->where('kegiatan', $this->filters['kegiatan']);
            }

            // Only missing tanda terima
            $query->whereDoesntHave('tandaTerima');

            $rows = $query->with('order.pengirim')->orderBy('created_at', 'desc')->get()->map(function($s) {
                return [
                    $s->no_surat_jalan,
                    $s->tanggal_surat_jalan ? $s->tanggal_surat_jalan->format('d/M/Y') : '-',
                    $s->no_kontainer,
                    $s->supir,
                    $s->no_plat,
                    $s->kegiatan,
                    optional($s->order->pengirim)->nama_pengirim ?? '-',
                ];
            });

            return $rows;
        }

        // If mode is 'combined', export TandaTerima rows followed by missing SuratJalan rows mapped to same schema
        if ($this->mode === 'combined') {
            // Get TandaTerima rows (same as below)
            $ttQuery = TandaTerima::with(['suratJalan.order.pengirim']);
            if (!empty($this->filters['search'])) {
                $search = $this->filters['search'];
                $ttQuery->where(function($q) use ($search) {
                    $q->where('no_surat_jalan', 'like', "%{$search}%")
                      ->orWhere('no_kontainer', 'like', "%{$search}%")
                      ->orWhere('estimasi_nama_kapal', 'like', "%{$search}%")
                      ->orWhere('tujuan_pengiriman', 'like', "%{$search}%")
                      ->orWhere('jenis_barang', 'like', "%{$search}%");
                });
            }
            if (!empty($this->filters['status'])) {
                $ttQuery->where('status', $this->filters['status']);
            }

            if (!empty($this->filters['kegiatan'])) {
                $ttQuery->where('kegiatan', $this->filters['kegiatan']);
            }

            $ttRows = $ttQuery->orderBy('created_at', 'desc')->get()->map(function($t) {
                $kegiatanName = MasterKegiatan::where('kode_kegiatan', $t->kegiatan)->value('nama_kegiatan') ?? $t->kegiatan;
                $tanggal = data_get($t, 'suratJalan.tanggal_surat_jalan') ? \Carbon\Carbon::parse(data_get($t, 'suratJalan.tanggal_surat_jalan'))->format('d/M/Y') : ($t->tanggal_checkpoint_supir ? $t->tanggal_checkpoint_supir->format('d/M/Y') : '-');
                $tujuanAmbil = data_get($t, 'suratJalan.tujuan_pengambilan') ?: data_get($t, 'suratJalan.order.tujuan_ambil', '-');
                return [
                    $t->id,
                    'TT-' . $t->id,
                    $t->no_surat_jalan,
                    $tanggal,
                    $t->no_kontainer,
                    $t->jenis_barang,
                    $tujuanAmbil,
                    $t->tujuan_pengiriman ?: '-',
                    $kegiatanName,
                    $t->status,
                    data_get($t, 'suratJalan.order.pengirim.nama_pengirim', '-'),
                ];
            });

            // Now get missing SuratJalan rows and map them
            $sjQuery = SuratJalan::query();
            if (!empty($this->filters['search'])) {
                $search = $this->filters['search'];
                $sjQuery->where(function($q) use ($search) {
                    $q->where('no_surat_jalan', 'like', "%{$search}%")
                      ->orWhere('supir', 'like', "%{$search}%")
                      ->orWhere('no_kontainer', 'like', "%{$search}%")
                      ->orWhere('no_plat', 'like', "%{$search}%");
                });
            }
            if (!empty($this->filters['status'])) {
                $sjQuery->where('status', $this->filters['status']);
            }
            if (!empty($this->filters['kegiatan'])) {
                $sjQuery->where('kegiatan', $this->filters['kegiatan']);
            }
            $sjQuery->whereDoesntHave('tandaTerima');
            $sjQuery->whereHas('uangJalans', function($uangJalanQuery) {
                $uangJalanQuery->whereHas('pranotaUangJalan', function($pranotaQuery) {
                    $pranotaQuery->whereHas('pembayaranPranotaUangJalans', function($pembayaranQuery) {
                        $pembayaranQuery->where('status_pembayaran', 'paid');
                    });
                });
            });

            $sjRows = $sjQuery->with('order.pengirim')->orderBy('created_at', 'desc')->get()->map(function($s) {
                return [
                    '',
                    '',
                    $s->no_surat_jalan,
                    $s->tanggal_surat_jalan ? $s->tanggal_surat_jalan->format('d/M/Y') : '-',
                    $s->no_kontainer,
                    '-',
                    optional($s->order->pengirim)->nama_pengirim ?? '-',
                    '-',
                    $s->kegiatan,
                    'Belum Ada Tanda Terima',
                    optional($s->order->pengirim)->nama_pengirim ?? '-',
                ];
            });

            // Concatenate ttRows and sjRows
            return $ttRows->concat($sjRows);
        }

        // Otherwise export TandaTerima rows
        $query = TandaTerima::with(['suratJalan.order.pengirim']);
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('estimasi_nama_kapal', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%")
                  ->orWhere('jenis_barang', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['kegiatan'])) {
            $query->where('kegiatan', $this->filters['kegiatan']);
        }

        $rows = $query->orderBy('created_at', 'desc')->get()->map(function($t) {
            $kegiatanName = MasterKegiatan::where('kode_kegiatan', $t->kegiatan)->value('nama_kegiatan') ?? $t->kegiatan;
            $tanggal = data_get($t, 'suratJalan.tanggal_surat_jalan') ? \Carbon\Carbon::parse(data_get($t, 'suratJalan.tanggal_surat_jalan'))->format('d/M/Y') : ($t->tanggal_checkpoint_supir ? $t->tanggal_checkpoint_supir->format('d/M/Y') : '-');
            $tujuanAmbil = data_get($t, 'suratJalan.tujuan_pengambilan') ?: data_get($t, 'suratJalan.order.tujuan_ambil', '-');
            return [
                $t->id,
                'TT-' . $t->id,
                $t->no_surat_jalan,
                $tanggal,
                $t->no_kontainer,
                $t->jenis_barang,
                $tujuanAmbil,
                $t->tujuan_pengiriman ?: '-',
                $kegiatanName,
                $t->status,
                data_get($t, 'suratJalan.order.pengirim.nama_pengirim', '-'),
            ];
        });

        return $rows;
    }

    public function headings(): array
    {
        if ($this->mode === 'missing') {
            return ['No. Surat Jalan', 'Tanggal', 'No. Kontainer', 'Supir', 'No. Plat', 'Kegiatan', 'Pengirim'];
        }

        // For Tanda Terima and combined mode, use the Tanda Terima heading layout
        return ['ID', 'ID Tanda Terima', 'No. Surat Jalan', 'Tanggal', 'No. Kontainer', 'Jenis Barang', 'Tujuan Ambil', 'Tujuan Kirim', 'Kegiatan', 'Status', 'Pengirim'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:Z1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
