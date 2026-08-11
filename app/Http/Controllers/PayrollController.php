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
        $group = $request->group;
        $subGroup = $request->sub_group;
        $cabang = $request->cabang;

        $payrolls = [];
        $isGenerated = $request->has('generate');

        if ($isGenerated) {
            $query = \App\Models\Karyawan::where('status', 'active');
            if (!empty($penempatan)) {
                $query->where('penempatan', $penempatan);
            }
            if (!empty($group)) {
                $query->where(function($q) use ($group) {
                    $q->where('grup', 'LIKE', '%"' . $group . ':%')
                      ->orWhere('grup', 'LIKE', '%"' . $group . '"%');
                });
            }
            if (!empty($subGroup)) {
                $query->where('grup', 'LIKE', '%:' . $subGroup . '"%');
            }
            if (!empty($cabang)) {
                $query->where('cabang', $cabang);
            }
            
            $karyawans = $query->with(['absensi' => function($q) use ($startDate, $endDate) {
                    $q->whereBetween('waktu', [$startDate->startOfDay(), $endDate->endOfDay()])
                      ->where('tipe', 'Masuk');
                }, 'uangMakanTerbaru'])->orderBy('nama_lengkap', 'asc')->get();

            foreach ($karyawans as $k) {
                $isSatpam = false;
                $kGrup = is_string($k->grup) ? json_decode($k->grup, true) : (array)$k->grup;
                if (is_array($kGrup)) {
                    foreach ($kGrup as $g) {
                        if (stripos($g, 'SATPAM GARASI') !== false || stripos($g, 'SATPAM PELABUHAN') !== false) {
                            $isSatpam = true;
                            break;
                        }
                    }
                }

                // Count unique days they clocked in
                $uniqueDaysDates = $k->absensi->filter(function($abs) use ($isSatpam) {
                    if ($isSatpam) return true;
                    return !\Carbon\Carbon::parse($abs->waktu)->isSunday();
                })->map(function($abs) {
                    return \Carbon\Carbon::parse($abs->waktu)->format('Y-m-d');
                })->unique()->values();
                $uniqueDays = $uniqueDaysDates->count();

                if ($uniqueDays > 0) {
                    // Determine multiplier based on penempatan
                    $multiplier = 1;
                    if (strcasecmp(trim($k->penempatan), 'Pelabuhan 1') === 0 || $k->penempatan == '1') {
                        $multiplier = 2;
                    }
                    
                    $karyawanNominalDasar = $k->uangMakanTerbaru ? $k->uangMakanTerbaru->nominal : ($k->nominal_uang_makan ?? 0);
                    $totalPayout = $uniqueDays * $multiplier * $karyawanNominalDasar;

                    $payrolls[] = [
                        'karyawan' => $k,
                        'total_kehadiran' => $uniqueDays,
                        'dates_kehadiran' => $uniqueDaysDates->toArray(),
                        'multiplier' => $multiplier,
                        'nominal_per_hari' => $karyawanNominalDasar,
                        'total_payout' => $totalPayout,
                    ];
                }
            }
        }

        // Fetch all unique groups and subgroups for the view
        $allKaryawans = \App\Models\Karyawan::where('status', 'active')->get(['grup']);
        $allGroups = [];
        $allSubGroups = [];
        foreach($allKaryawans as $k) {
            $kGrup = is_string($k->grup) ? json_decode($k->grup, true) : (array)$k->grup;
            if(is_array($kGrup)) {
                foreach($kGrup as $g) {
                    $parts = explode(':', $g, 2);
                    if($parts[0] !== '' && !in_array($parts[0], $allGroups)) $allGroups[] = $parts[0];
                    if(isset($parts[1]) && $parts[1] !== '' && !in_array($parts[1], $allSubGroups)) $allSubGroups[] = $parts[1];
                }
            }
        }
        sort($allGroups);
        sort($allSubGroups);

        $allCabang = \App\Models\Karyawan::where('status', 'active')->whereNotNull('cabang')->where('cabang', '!=', '')->distinct()->pluck('cabang')->sort()->values();

        return view('payroll.uang-makan', compact('startDate', 'endDate', 'penempatan', 'group', 'subGroup', 'cabang', 'allGroups', 'allSubGroups', 'allCabang', 'payrolls', 'isGenerated'));
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
        $group = $request->group;
        $subGroup = $request->sub_group;
        $cabang = $request->cabang;

        $query = \App\Models\Karyawan::where('status', 'active');
        if (!empty($penempatan)) {
            $query->where('penempatan', $penempatan);
        }
        if (!empty($group)) {
            $query->where(function($q) use ($group) {
                $q->where('grup', 'LIKE', '%"' . $group . ':%')
                  ->orWhere('grup', 'LIKE', '%"' . $group . '"%');
            });
        }
        if (!empty($subGroup)) {
            $query->where('grup', 'LIKE', '%:' . $subGroup . '"%');
        }
        if (!empty($cabang)) {
            $query->where('cabang', $cabang);
        }
        
        $karyawans = $query->with(['absensi' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('waktu', [$startDate->startOfDay(), $endDate->endOfDay()])
                  ->where('tipe', 'Masuk');
            }, 'uangMakanTerbaru'])->orderBy('nama_lengkap', 'asc')->get();

        $submittedPayrolls = $request->input('payrolls', []);
        $count = 0;
        foreach ($karyawans as $k) {
            $isSatpam = false;
            $kGrup = is_string($k->grup) ? json_decode($k->grup, true) : (array)$k->grup;
            if (is_array($kGrup)) {
                foreach ($kGrup as $g) {
                    if (stripos($g, 'SATPAM GARASI') !== false || stripos($g, 'SATPAM PELABUHAN') !== false) {
                        $isSatpam = true;
                        break;
                    }
                }
            }

            $uniqueDaysDates = $k->absensi->filter(function($abs) use ($isSatpam) {
                if ($isSatpam) return true;
                return !\Carbon\Carbon::parse($abs->waktu)->isSunday();
            })->map(function($abs) {
                return \Carbon\Carbon::parse($abs->waktu)->format('Y-m-d');
            })->unique()->values();
            $uniqueDays = $uniqueDaysDates->count();

            if ($uniqueDays > 0) {
                $multiplier = 1;
                if (strcasecmp(trim($k->penempatan), 'Pelabuhan 1') === 0 || $k->penempatan == '1') {
                    $multiplier = 2;
                }
                
                // Prioritaskan nilai dari form input manual, jika tidak ada gunakan data Uang Makan terbaru
                $karyawanNominalDasar = $submittedPayrolls[$k->id]['nominal_per_hari'] ?? ($k->uangMakanTerbaru ? $k->uangMakanTerbaru->nominal : ($k->nominal_uang_makan ?? 0));
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
