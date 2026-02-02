<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PranotaOb;
use Carbon\Carbon;

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

        // Group items by voyage for CSV
        $groupedByVoyage = $items->groupBy('no_voyage');

        $filename = 'Report_Pranota_OB_' . $request->dari_tanggal . '_to_' . $request->sampai_tanggal . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($groupedByVoyage) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, ['No', 'Tanggal', 'Voyage', 'NIK', 'Supir', 'Total']);

            $no = 1;
            $totalKeseluruhan = 0;

            foreach ($groupedByVoyage as $voyage => $items) {
                // Add voyage header row
                fputcsv($file, ['', 'VOYAGE: ' . ($voyage ?? 'Tidak Ada Voyage'), '', '', '', '']);
                
                $subtotalVoyage = 0;
                foreach ($items as $item) {
                    $totalKeseluruhan += $item->total_biaya;
                    $subtotalVoyage += $item->total_biaya;
                    
                    fputcsv($file, [
                        $no++,
                        Carbon::parse($item->tanggal_ob)->format('d/m/Y'),
                        $item->no_voyage ?? '-',
                        $item->nik ?? '-',
                        $item->supir ?? '-',
                        number_format($item->total_biaya, 0, ',', '.'),
                    ]);
                }
                
                // Add subtotal row for this voyage
                fputcsv($file, ['', '', '', '', 'Subtotal ' . ($voyage ?? 'Tidak Ada Voyage') . ':', number_format($subtotalVoyage, 0, ',', '.')]);
                fputcsv($file, ['', '', '', '', '', '']); // Empty row for separation
            }

            // Add total row
            fputcsv($file, ['', '', '', '', 'TOTAL:', number_format($totalKeseluruhan, 0, ',', '.')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
