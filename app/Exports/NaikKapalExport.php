<?php

namespace App\Exports;

use App\Models\NaikKapal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class NaikKapalExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $kapalNama;
    protected $noVoyage;

    public function __construct(array $filters = [], $kapalNama = '', $noVoyage = '')
    {
        $this->filters = $filters;
        $this->kapalNama = $kapalNama;
        $this->noVoyage = $noVoyage;
    }

    public function collection()
    {
        $query = NaikKapal::with(['prospek.tandaTerima'])
            ->where('nama_kapal', $this->kapalNama)
            ->where('no_voyage', $this->noVoyage)
            ->orderBy('created_at', 'desc');

        // Search functionality
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nomor_kontainer', 'like', "%{$search}%")
                  ->orWhere('jenis_barang', 'like', "%{$search}%")
                  ->orWhere('no_seal', 'like', "%{$search}%")
                  ->orWhere('ukuran_kontainer', 'like', "%{$search}%");
            });
        }
        
        // Filter by status BL
        if (!empty($this->filters['status_bl'])) {
            if ($this->filters['status_bl'] === 'sudah_bl') {
                $query->where('status', 'Moved to BLS');
            } elseif ($this->filters['status_bl'] === 'belum_bl') {
                $query->where(function($q) {
                    $q->where('status', '!=', 'Moved to BLS')
                      ->orWhereNull('status');
                });
            }
        }
        
        // Filter by tipe kontainer
        if (!empty($this->filters['tipe_kontainer'])) {
            $query->where('tipe_kontainer', $this->filters['tipe_kontainer']);
        }
        
        // Legacy status filter support
        if (!empty($this->filters['status_filter'])) {
            if ($this->filters['status_filter'] === 'sudah_bl') {
                $query->where('status', 'Moved to BLS');
            } elseif ($this->filters['status_filter'] === 'belum_bl') {
                $query->where(function($q) {
                    $q->where('status', '!=', 'Moved to BLS')
                      ->orWhereNull('status');
                });
            }
        }

        $naikKapals = $query->get();

        return $naikKapals->map(function($naikKapal, $index) {
            $prospek = $naikKapal->prospek;
            $tandaTerima = $prospek ? $prospek->tandaTerima : null;

            // Ambil volume dan tonase dari prospek jika ada
            $volume = $prospek->total_volume ?? $naikKapal->total_volume ?? 0;
            $tonase = $prospek->total_ton ?? $naikKapal->total_tonase ?? 0;
            $kuantitas = $prospek->kuantitas ?? $naikKapal->kuantitas ?? 0;

            return [
                $index + 1,
                $naikKapal->nomor_kontainer ?: '-',
                $naikKapal->ukuran_kontainer ?: '-',
                $naikKapal->no_seal ?: '-',
                $naikKapal->jenis_barang ?: '-',
                $naikKapal->tipe_kontainer ?: '-',
                $naikKapal->tipe_kontainer_detail ?: '-',
                number_format($volume, 3, ',', '.') . ' m³',
                number_format($tonase, 3, ',', '.') . ' Ton',
                $kuantitas ?: '0',
                $naikKapal->nama_kapal ?: '-',
                $naikKapal->no_voyage ?: '-',
                $naikKapal->pelabuhan_tujuan ?: '-',
                $naikKapal->tanggal_muat ? date('d/m/Y', strtotime($naikKapal->tanggal_muat)) : '-',
                $naikKapal->jam_muat ? date('H:i', strtotime($naikKapal->jam_muat)) : '-',
                $naikKapal->pelabuhan_asal ?: '-',
                $prospek ? $prospek->id : '-',
                $prospek ? $prospek->nama_supir : '-',
                $prospek ? $prospek->pt_pengirim : ($tandaTerima ? $tandaTerima->pengirim : '-'),
                $tandaTerima ? $tandaTerima->penerima : '-',
                $naikKapal->status === 'Moved to BLS' ? 'Sudah BL' : 'Belum BL',
                $naikKapal->status ?: 'Active'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Kontainer',
            'Ukuran',
            'No. Seal',
            'Jenis Barang',
            'Tipe Kontainer',
            'Detail Tipe',
            'Volume',
            'Tonase',
            'Kuantitas',
            'Nama Kapal',
            'No. Voyage',
            'Pelabuhan Tujuan',
            'Tanggal Muat',
            'Jam Muat',
            'Pelabuhan Asal',
            'Prospek ID',
            'Nama Supir',
            'Pengirim',
            'Penerima',
            'Status BL',
            'Status'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Style header row
                $event->sheet->getStyle('A1:V1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '7C3AED'], // Purple-600
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Add borders to all data cells
                $highestRow = $event->sheet->getHighestRow();
                $highestColumn = $event->sheet->getHighestColumn();
                
                $event->sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                // Center align for specific columns
                $event->sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('C2:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('D2:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('F2:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('J2:J' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('N2:O' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('Q2:Q' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('U2:V' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Set row height for header
                $event->sheet->getRowDimension(1)->setRowHeight(25);

                // Auto-wrap text for some columns
                $event->sheet->getStyle('E2:E' . $highestRow)->getAlignment()->setWrapText(true);
            },
        ];
    }
}
