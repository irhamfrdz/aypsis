<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        return view('payroll.index');
    }

    public function uangMakan(Request $request)
    {
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : now()->startOfWeek();
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date) : now()->endOfWeek();
        $penempatan = $request->penempatan;

        $payrolls = [];
        $isGenerated = $request->has('generate');

        if ($isGenerated) {
            // Get all karyawans with their attendance grouped by date
            $query = \App\Models\Karyawan::where('status', 'active');
            if (!empty($penempatan)) {
                $query->where('penempatan', $penempatan);
            }
            
            $karyawans = $query->with(['absensi' => function($q) use ($startDate, $endDate) {
                    $q->whereBetween('waktu', [$startDate->startOfDay(), $endDate->endOfDay()])
                      ->where('tipe', 'Masuk');
                }])->get();

            foreach ($karyawans as $k) {
                // Count unique days they clocked in
                $uniqueDays = $k->absensi->map(function($abs) {
                    return \Carbon\Carbon::parse($abs->waktu)->format('Y-m-d');
                })->unique()->count();

                if ($uniqueDays > 0) {
                    // Determine multiplier based on penempatan
                    $multiplier = 1;
                    if (strcasecmp(trim($k->penempatan), 'Pelabuhan 1') === 0 || $k->penempatan == '1') {
                        $multiplier = 2;
                    }
                    
                    $karyawanNominalDasar = $k->nominal_uang_makan ?? 0;
                    $totalPayout = $uniqueDays * $multiplier * $karyawanNominalDasar;

                    $payrolls[] = [
                        'karyawan' => $k,
                        'total_kehadiran' => $uniqueDays,
                        'multiplier' => $multiplier,
                        'nominal_per_hari' => $karyawanNominalDasar,
                        'total_payout' => $totalPayout,
                    ];
                }
            }
        }

        return view('payroll.uang-makan', compact('startDate', 'endDate', 'penempatan', 'payrolls', 'isGenerated'));
    }

    public function storeUangMakan(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $penempatan = $request->penempatan;
        
        // Get karyawans and recalculate payout
        $query = \App\Models\Karyawan::where('status', 'active');
        if (!empty($penempatan)) {
            $query->where('penempatan', $penempatan);
        }
        
        $karyawans = $query->with(['absensi' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('waktu', [$startDate->startOfDay(), $endDate->endOfDay()])
                  ->where('tipe', 'Masuk');
            }])->get();

        $submittedPayrolls = $request->input('payrolls', []);
        $count = 0;
        foreach ($karyawans as $k) {
            $uniqueDays = $k->absensi->map(function($abs) {
                return \Carbon\Carbon::parse($abs->waktu)->format('Y-m-d');
            })->unique()->count();

            if ($uniqueDays > 0) {
                $multiplier = 1;
                if (strcasecmp(trim($k->penempatan), 'Pelabuhan 1') === 0 || $k->penempatan == '1') {
                    $multiplier = 2;
                }
                
                // Prioritaskan nilai dari form input manual, jika tidak ada gunakan data Karyawan
                $karyawanNominalDasar = $submittedPayrolls[$k->id]['nominal_per_hari'] ?? ($k->nominal_uang_makan ?? 0);
                $totalPayout = $uniqueDays * $multiplier * $karyawanNominalDasar;

                \App\Models\PayrollUangMakan::updateOrCreate(
                    [
                        'karyawan_id' => $k->id,
                        'periode_start' => $startDate->format('Y-m-d'),
                        'periode_end' => $endDate->format('Y-m-d'),
                    ],
                    [
                        'total_kehadiran' => $uniqueDays,
                        'multiplier' => $multiplier,
                        'nominal_per_hari' => $karyawanNominalDasar,
                        'total_payout' => $totalPayout,
                        'status' => 'draft',
                    ]
                );
                $count++;
            }
        }

        $formattedStartDate = $startDate->format('Y-m-d');
        $formattedEndDate = $endDate->format('Y-m-d');
        $fileName = 'payroll_uang_makan_' . $formattedStartDate . '_sd_' . $formattedEndDate . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PayrollUangMakanExport($formattedStartDate, $formattedEndDate, $penempatan),
            $fileName
        );
    }
}
