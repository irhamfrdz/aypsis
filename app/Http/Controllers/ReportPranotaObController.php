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
            ->leftJoin(\DB::raw('(SELECT supir_name, MIN(nik) as nik, MAX(real_nama_lengkap) as nama_lengkap FROM (
                SELECT nama_panggilan as supir_name, nik, nama_lengkap as real_nama_lengkap FROM karyawans WHERE nama_panggilan IS NOT NULL
                UNION
                SELECT nama_lengkap as supir_name, nik, nama_lengkap as real_nama_lengkap FROM karyawans WHERE nama_lengkap IS NOT NULL
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
                'karyawans.nama_lengkap',
                'pranota_obs.nomor_pranota',
                'pranota_obs.id as pranota_id',
                \DB::raw('SUM(pranota_ob_items.biaya) as total_biaya')
            )
            ->groupBy('pranota_obs.no_voyage', 'pranota_ob_items.supir', 'pranota_obs.tanggal_ob', 'karyawans.nik', 'karyawans.nama_lengkap', 'pranota_obs.nomor_pranota', 'pranota_obs.id')
            ->orderBy('pranota_obs.tanggal_ob', 'desc')
            ->orderBy('pranota_obs.no_voyage', 'asc')
            ->orderBy('pranota_ob_items.supir', 'asc')
            ->get();

        // Flatten items for simple listing
        $allItems = $items;
        
        // Build container details for each item
        $containerDetails = [];
        foreach ($allItems as $item) {
            $key = $item->pranota_id . '_' . $item->supir;
            
            // Get detailed container info for this supir from this pranota
            $details = \DB::table('pranota_ob_items')
                ->where('pranota_ob_id', $item->pranota_id)
                ->where('supir', $item->supir)
                ->whereNotNull('biaya')
                ->where('biaya', '>', 0)
                ->select('size', 'status')
                ->get();
            
            // Count containers by size and status
            $counts = [];
            foreach ($details as $detail) {
                $size = strtolower($detail->size ?? '');
                $status = strtolower($detail->status ?? 'full');
                
                // Normalize size (20ft, 20, 20 ft, etc -> 20ft)
                if (preg_match('/20/', $size)) {
                    $size = '20ft';
                } elseif (preg_match('/40/', $size)) {
                    $size = '40ft';
                } else {
                    $size = $detail->size ?? 'unknown';
                }
                
                // Normalize status
                if (!in_array($status, ['full', 'empty'])) {
                    $status = 'full'; // default
                }
                
                $sizeStatusKey = $size . '_' . $status;
                if (!isset($counts[$sizeStatusKey])) {
                    $counts[$sizeStatusKey] = 0;
                }
                $counts[$sizeStatusKey]++;
            }
            
            // Build keterangan string like "20ft full 5x, 40ft empty 3x"
            $keteranganParts = [];
            foreach ($counts as $sizeStatus => $count) {
                list($sz, $st) = explode('_', $sizeStatus);
                $keteranganParts[] = $sz . ' ' . $st . ' ' . $count . 'x';
            }
            $containerDetails[$key] = implode(', ', $keteranganParts);
        }

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
        $sheet->setCellValue('D1', 'Nomor Pranota');
        $sheet->setCellValue('E1', 'NIK');
        $sheet->setCellValue('F1', 'Supir');
        $sheet->setCellValue('G1', 'Total');
        $sheet->setCellValue('H1', 'Keterangan');
        
        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(50);

        $row = 2;
        $no = 1;
        $totalKeseluruhan = 0;

        // Loop through all items directly without grouping
        foreach ($allItems as $item) {
            // Skip items with invalid supir or zero total
            if (!$item->supir || $item->total_biaya <= 0) {
                continue;
            }
            
            $totalKeseluruhan += $item->total_biaya;
            
            // Use nama_lengkap if available, otherwise fallback to supir (panggilan)
            $displayName = $item->nama_lengkap ?? $item->supir;
            
            // Get keterangan for this item
            $keteranganKey = $item->pranota_id . '_' . $item->supir;
            $keterangan = $containerDetails[$keteranganKey] ?? '-';

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, Carbon::parse($item->tanggal_ob)->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $item->no_voyage ?? '-');
            $sheet->setCellValue('D' . $row, $item->nomor_pranota ?? '-');
            $sheet->setCellValue('E' . $row, $item->nik ?? '-');
            $sheet->setCellValue('F' . $row, $displayName);
            $sheet->setCellValue('G' . $row, 'Rp ' . number_format($item->total_biaya, 0, ',', '.'));
            $sheet->setCellValue('H' . $row, $keterangan);
            
            // Style data rows with alternating colors
            $bgColor = ($row % 2 == 0) ? 'F2F2F2' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            $row++;
        }

        // Add total keseluruhan row
        $sheet->setCellValue('F' . $row, 'TOTAL KESELURUHAN:');
        $sheet->setCellValue('G' . $row, 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'));
        $totalStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC000']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ];
        $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($totalStyle);
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

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
