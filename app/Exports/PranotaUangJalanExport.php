<?php

namespace App\Exports;

use App\Models\InvoiceAktivitasLain;
use App\Models\PembayaranInvoiceAktivitasLain;
use App\Models\PranotaUangJalan;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PranotaUangJalanExport implements WithMultipleSheets
{
    protected $filters;

    protected $pranotaIds;

    public function __construct(array $filters = [], array $pranotaIds = [])
    {
        $this->filters = $filters;
        $this->pranotaIds = $pranotaIds;
    }

    public function sheets(): array
    {
        return [
            'Pranota Uang Jalan' => new PranotaUangJalanSheet($this->filters, $this->pranotaIds),
            'Invoice Aktivitas Lain' => new InvoiceAktivitasLainSheet($this->filters),
            'Pembayaran Aktivitas Lain' => new PembayaranAktivitasLainSheet($this->filters),
        ];
    }
}

// ── Sheet 1: Pranota Uang Jalan ──────────────────────────────────────────────

class PranotaUangJalanSheet implements \Maatwebsite\Excel\Concerns\FromCollection, ShouldAutoSize, WithEvents, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle
{
    protected $filters;

    protected $pranotaIds;

    public function __construct(array $filters = [], array $pranotaIds = [])
    {
        $this->filters = $filters;
        $this->pranotaIds = $pranotaIds;
    }

    public function title(): string
    {
        return 'Pranota Uang Jalan';
    }

    public function collection()
    {
        if (! empty($this->pranotaIds)) {
            $query = PranotaUangJalan::with(['uangJalans.suratJalan', 'uangJalans.suratJalanBongkaran', 'uangJalans.suratJalanBongkaranBatam'])
                ->whereIn('id', $this->pranotaIds);
        } else {
            $query = PranotaUangJalan::with(['uangJalans.suratJalan', 'uangJalans.suratJalanBongkaran', 'uangJalans.suratJalanBongkaranBatam'])
                ->orderBy('created_at', 'desc');

            if (! empty($this->filters['search'])) {
                $search = $this->filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_pranota', 'like', "%{$search}%")
                        ->orWhereHas('uangJalans', function ($sq) use ($search) {
                            $sq->where('nomor_uang_jalan', 'like', "%{$search}%");
                        });
                });
            }

            if (! empty($this->filters['status'])) {
                $query->where('status_pembayaran', $this->filters['status']);
            }

            if (! empty($this->filters['tanggal_dari'])) {
                $query->whereDate('tanggal_pranota', '>=', $this->filters['tanggal_dari']);
            }

            if (! empty($this->filters['tanggal_sampai'])) {
                $query->whereDate('tanggal_pranota', '<=', $this->filters['tanggal_sampai']);
            }
        }

        $rows = collect();

        $query->get()->each(function ($p) use (&$rows) {
            // Collect all nomor surat jalan from uang jalans
            $nomorSuratJalans = [];
            foreach ($p->uangJalans as $uj) {
                if ($uj->suratJalan) {
                    $nomorSuratJalans[] = $uj->suratJalan->no_surat_jalan;
                }
                if ($uj->suratJalanBongkaran) {
                    $nomorSuratJalans[] = $uj->suratJalanBongkaran->nomor_surat_jalan;
                }
                if ($uj->suratJalanBongkaranBatam) {
                    $nomorSuratJalans[] = $uj->suratJalanBongkaranBatam->nomor_surat_jalan;
                }
            }

            $nomorSuratJalans = array_unique(array_filter($nomorSuratJalans));

            $rows->push([
                $p->nomor_pranota,
                $p->tanggal_pranota ? (is_string($p->tanggal_pranota) ? \Carbon\Carbon::parse($p->tanggal_pranota)->format('d/m/Y') : $p->tanggal_pranota->format('d/m/Y')) : '-',
                $p->jumlah_uang_jalan,
                $p->total_amount,
                $p->status_pembayaran,
                $p->periode_tagihan,
                implode(', ', $nomorSuratJalans) ?: '-',
                $p->createdBy->username ?? 'System',
            ]);
        });

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Nomor Pranota',
            'Tanggal Pranota',
            'Jumlah Uang Jalan',
            'Total Amount',
            'Status Pembayaran',
            'Periode Tagihan',
            'Nomor Surat Jalan',
            'Created By',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $range = 'A1:H' . $lastRow;

                // Header styling
                $sheet->getStyle('A1:H1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Border
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            },
        ];
    }
}

// ── Sheet 2: Invoice Aktivitas Lain ──────────────────────────────────────────

class InvoiceAktivitasLainSheet implements \Maatwebsite\Excel\Concerns\FromCollection, ShouldAutoSize, WithEvents, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Invoice Aktivitas Lain';
    }

    public function collection()
    {
        $query = InvoiceAktivitasLain::with(['suratJalan', 'klasifikasiBiaya'])
            ->orderBy('created_at', 'desc');

        if (! empty($this->filters['tanggal_dari'])) {
            $query->whereDate('tanggal_invoice', '>=', $this->filters['tanggal_dari']);
        }

        if (! empty($this->filters['tanggal_sampai'])) {
            $query->whereDate('tanggal_invoice', '<=', $this->filters['tanggal_sampai']);
        }

        return $query->get()->map(function ($inv) {
            return [
                $inv->nomor_invoice,
                $inv->tanggal_invoice ? (is_string($inv->tanggal_invoice) ? \Carbon\Carbon::parse($inv->tanggal_invoice)->format('d/m/Y') : $inv->tanggal_invoice->format('d/m/Y')) : '-',
                $inv->jenis_aktivitas,
                $inv->referensi,
                $inv->penerima,
                $inv->subtotal,
                $inv->pph,
                $inv->biaya_materai,
                $inv->biaya_adjustment,
                $inv->grand_total,
                $inv->status,
                $inv->jenis_penyesuaian,
                $inv->deskripsi,
                $inv->keterangan,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nomor Invoice',
            'Tanggal Invoice',
            'Jenis Aktivitas',
            'Referensi',
            'Penerima',
            'Subtotal',
            'PPH',
            'Biaya Materai',
            'Biaya Adjustment',
            'Grand Total',
            'Status',
            'Jenis Penyesuaian',
            'Deskripsi',
            'Keterangan',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $range = 'A1:N' . $lastRow;

                $sheet->getStyle('A1:N1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle($range)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            },
        ];
    }
}

// ── Sheet 3: Pembayaran Aktivitas Lain ───────────────────────────────────────

class PembayaranAktivitasLainSheet implements \Maatwebsite\Excel\Concerns\FromCollection, ShouldAutoSize, WithEvents, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Pembayaran Aktivitas Lain';
    }

    public function collection()
    {
        $query = PembayaranInvoiceAktivitasLain::with(['invoices', 'akunCoa', 'akunBank'])
            ->orderBy('created_at', 'desc');

        if (! empty($this->filters['tanggal_dari'])) {
            $query->whereDate('tanggal', '>=', $this->filters['tanggal_dari']);
        }

        if (! empty($this->filters['tanggal_sampai'])) {
            $query->whereDate('tanggal', '<=', $this->filters['tanggal_sampai']);
        }

        return $query->get()->map(function ($p) {
            // Collect related invoice numbers
            $nomorInvoices = $p->invoices->pluck('nomor_invoice')->filter()->implode(', ') ?: '-';

            return [
                $p->nomor,
                $p->nomor_accurate,
                $p->tanggal ? (is_string($p->tanggal) ? \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') : $p->tanggal->format('d/m/Y')) : '-',
                $p->jenis_aktivitas,
                $p->penerima,
                $p->total_invoice,
                $p->jumlah_dibayar,
                $p->debit_kredit,
                $p->akunCoa->nama ?? '-',
                $p->akunBank->nama ?? '-',
                $nomorInvoices,
                $p->status,
                $p->keterangan,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nomor Pembayaran',
            'Nomor Accurate',
            'Tanggal',
            'Jenis Aktivitas',
            'Penerima',
            'Total Invoice',
            'Jumlah Dibayar',
            'Debit/Kredit',
            'Akun COA',
            'Akun Bank',
            'Nomor Invoice',
            'Status',
            'Keterangan',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $range = 'A1:M' . $lastRow;

                $sheet->getStyle('A1:M1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D97706']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle($range)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            },
        ];
    }
}
