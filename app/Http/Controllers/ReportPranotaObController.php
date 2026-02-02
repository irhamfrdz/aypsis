<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PranotaOb;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportPranotaObController extends Controller
{
    /**
     * Display the select date page
     */
    public function index()
    {
        return view('reports.pranota-ob.select-date');
    }

    /**
     * View report based on date range
     */
    public function view(Request $request)
    {
        // Validasi required tanggal
        if (!$request->has('dari_tanggal') || !$request->has('sampai_tanggal')) {
            return redirect()->route('report.pranota-ob.index')
                ->with('error', 'Tanggal mulai dan tanggal akhir harus diisi');
        }

        $request->validate([
            'dari_tanggal' => 'required|date',
            'sampai_tanggal' => 'required|date|after_or_equal:dari_tanggal',
        ], [
            'dari_tanggal.required' => 'Dari tanggal harus diisi',
            'sampai_tanggal.required' => 'Sampai tanggal harus diisi',
            'sampai_tanggal.after_or_equal' => 'Sampai tanggal harus sama atau setelah dari tanggal',
        ]);

        $dariTanggal = Carbon::parse($request->dari_tanggal)->startOfDay();
        $sampaiTanggal = Carbon::parse($request->sampai_tanggal)->endOfDay();

        // Get items grouped by voyage and supir
        $items = \DB::table('pranota_ob_items')
            ->join('pranota_obs', 'pranota_ob_items.pranota_ob_id', '=', 'pranota_obs.id')
            ->leftJoin(\DB::raw('(SELECT supir_name, MIN(nik) as nik FROM (
                SELECT nama_panggilan as supir_name, nik FROM karyawans WHERE nama_panggilan IS NOT NULL
                UNION
                SELECT nama_lengkap as supir_name, nik FROM karyawans WHERE nama_lengkap IS NOT NULL
            ) sub GROUP BY supir_name) karyawans'), 'pranota_ob_items.supir', '=', 'karyawans.supir_name')
            ->whereBetween('pranota_obs.tanggal_ob', [$dariTanggal, $sampaiTanggal])
            ->select(
                'pranota_obs.tanggal_ob',
                'pranota_obs.no_voyage',
                'pranota_ob_items.supir',
                'karyawans.nik',
                \DB::raw('SUM(pranota_ob_items.biaya) as total_biaya')
            )
            ->groupBy('pranota_obs.no_voyage', 'pranota_ob_items.supir', 'pranota_obs.tanggal_ob', 'karyawans.nik')
            ->orderBy('pranota_obs.tanggal_ob', 'desc')
            ->orderBy('pranota_obs.no_voyage', 'asc')
            ->orderBy('pranota_ob_items.supir', 'asc')
            ->get();

        // Group items by voyage
        $groupedByVoyage = $items->groupBy('no_voyage');
        
        $totalKeseluruhan = $items->sum('total_biaya');

        return view('reports.pranota-ob.view', compact('groupedByVoyage', 'totalKeseluruhan', 'dariTanggal', 'sampaiTanggal'));
    }

    /**
     * Print report
     */
    public function print(Request $request)
    {
        // Validasi required tanggal
        if (!$request->has('dari_tanggal') || !$request->has('sampai_tanggal')) {
            return redirect()->route('report.pranota-ob.index')
                ->with('error', 'Tanggal mulai dan tanggal akhir harus diisi');
        }

        $request->validate([
            'dari_tanggal' => 'required|date',
            'sampai_tanggal' => 'required|date|after_or_equal:dari_tanggal',
        ]);

        $dariTanggal = Carbon::parse($request->dari_tanggal)->startOfDay();
        $sampaiTanggal = Carbon::parse($request->sampai_tanggal)->endOfDay();

        // Get items grouped by voyage and supir
        $items = \DB::table('pranota_ob_items')
            ->join('pranota_obs', 'pranota_ob_items.pranota_ob_id', '=', 'pranota_obs.id')
            ->leftJoin(\DB::raw('(SELECT supir_name, MIN(nik) as nik FROM (
                SELECT nama_panggilan as supir_name, nik FROM karyawans WHERE nama_panggilan IS NOT NULL
                UNION
                SELECT nama_lengkap as supir_name, nik FROM karyawans WHERE nama_lengkap IS NOT NULL
            ) sub GROUP BY supir_name) karyawans'), 'pranota_ob_items.supir', '=', 'karyawans.supir_name')
            ->whereBetween('pranota_obs.tanggal_ob', [$dariTanggal, $sampaiTanggal])
            ->whereNotNull('pranota_ob_items.supir')
            ->where('pranota_ob_items.supir', '!=', '')
            ->whereNotNull('pranota_ob_items.biaya')
            ->where('pranota_ob_items.biaya', '>', 0)
            ->select(
                'pranota_obs.tanggal_ob',
                'pranota_obs.no_voyage',
                'pranota_ob_items.supir',
                'karyawans.nik',
                \DB::raw('SUM(pranota_ob_items.biaya) as total_biaya')
            )
            ->groupBy('pranota_obs.no_voyage', 'pranota_ob_items.supir', 'pranota_obs.tanggal_ob', 'karyawans.nik')
            ->orderBy('pranota_obs.tanggal_ob', 'desc')
            ->orderBy('pranota_obs.no_voyage', 'asc')
            ->orderBy('pranota_ob_items.supir', 'asc')
            ->get();

        // Group items by voyage
        $groupedByVoyage = $items->groupBy('no_voyage');
        
        $totalKeseluruhan = $items->sum('total_biaya');

        return view('reports.pranota-ob.print', compact('groupedByVoyage', 'totalKeseluruhan', 'dariTanggal', 'sampaiTanggal'));
    }

    /**
     * Export report to Excel
     */
    public function export(Request $request)
    {
        $request->validate([
            'dari_tanggal' => 'required|date',
            'sampai_tanggal' => 'required|date|after_or_equal:dari_tanggal',
        ]);

        $dariTanggal = Carbon::parse($request->dari_tanggal)->startOfDay();
        $sampaiTanggal = Carbon::parse($request->sampai_tanggal)->endOfDay();

        // Get items grouped by voyage and supir
        $items = \DB::table('pranota_ob_items')
            ->join('pranota_obs', 'pranota_ob_items.pranota_ob_id', '=', 'pranota_obs.id')
            ->leftJoin(\DB::raw('(SELECT supir_name, MIN(nik) as nik FROM (
                SELECT nama_panggilan as supir_name, nik FROM karyawans WHERE nama_panggilan IS NOT NULL
                UNION
                SELECT nama_lengkap as supir_name, nik FROM karyawans WHERE nama_lengkap IS NOT NULL
            ) sub GROUP BY supir_name) karyawans'), 'pranota_ob_items.supir', '=', 'karyawans.supir_name')
            ->whereBetween('pranota_obs.tanggal_ob', [$dariTanggal, $sampaiTanggal])
            ->whereNotNull('pranota_ob_items.supir')
            ->where('pranota_ob_items.supir', '!=', '')
            ->whereNotNull('pranota_ob_items.biaya')
            ->where('pranota_ob_items.biaya', '>', 0)
            ->select(
                'pranota_obs.tanggal_ob',
                'pranota_obs.no_voyage',
                'pranota_ob_items.supir',
                'karyawans.nik',
                \DB::raw('SUM(pranota_ob_items.biaya) as total_biaya')
            )
            ->groupBy('pranota_obs.no_voyage', 'pranota_ob_items.supir', 'pranota_obs.tanggal_ob', 'karyawans.nik')
            ->orderBy('pranota_obs.tanggal_ob', 'desc')
            ->orderBy('pranota_obs.no_voyage', 'asc')
            ->orderBy('pranota_ob_items.supir', 'asc')
            ->get();

        // Group items by voyage for Excel
        $groupedByVoyage = $items->groupBy('no_voyage');

        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('AYPSIS')
            ->setTitle('Report Pranota OB')
            ->setSubject('Report Pranota OB ' . $request->dari_tanggal . ' to ' . $request->sampai_tanggal);

        // Set header row
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Voyage');
        $sheet->setCellValue('D1', 'Supir');
        $sheet->setCellValue('E1', 'Total');
        
        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(20);

        $row = 2;
        $no = 1;
        $totalKeseluruhan = 0;

        foreach ($groupedByVoyage as $voyage => $items) {
            // Add voyage header row
            $sheet->setCellValue('A' . $row, 'VOYAGE: ' . ($voyage ?? 'Tidak Ada Voyage'));
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $voyageHeaderStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '5B9BD5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($voyageHeaderStyle);
            $row++;
            
            $subtotalVoyage = 0;
            foreach ($items as $item) {
                // Skip items with invalid supir or zero total
                if (!$item->supir || $item->total_biaya <= 0) {
                    continue;
                }
                
                $totalKeseluruhan += $item->total_biaya;
                $subtotalVoyage += $item->total_biaya;
                
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, Carbon::parse($item->tanggal_ob)->format('d/m/Y'));
                $sheet->setCellValue('C' . $row, $item->no_voyage ?? '-');
                $sheet->setCellValue('D' . $row, $item->supir ?? '-');
                $sheet->setCellValue('E' . $row, 'Rp ' . number_format($item->total_biaya, 0, ',', '.'));
                
                // Style data rows
                $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);
                $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                $row++;
            }
            
            // Add subtotal row for this voyage
            $sheet->setCellValue('D' . $row, 'Subtotal ' . ($voyage ?? 'Tidak Ada Voyage') . ':');
            $sheet->setCellValue('E' . $row, 'Rp ' . number_format($subtotalVoyage, 0, ',', '.'));
            $subtotalStyle = [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7E6E6']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($subtotalStyle);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $row++;
            
            // Empty row for separation
            $row++;
        }

        // Add total keseluruhan row
        $sheet->setCellValue('D' . $row, 'TOTAL KESELURUHAN:');
        $sheet->setCellValue('E' . $row, 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'));
        $totalStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC000']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ];
        $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($totalStyle);
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Create Excel file
        $filename = 'Report_Pranota_OB_' . $request->dari_tanggal . '_to_' . $request->sampai_tanggal . '.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
