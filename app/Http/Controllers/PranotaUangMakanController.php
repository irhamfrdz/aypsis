<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PranotaUangMakan;
use App\Models\PranotaUangMakanDetail;
use App\Models\Karyawan;
use App\Models\KaryawanTidakTetap;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\PranotaUangMakanAutoTransferExport;
use Maatwebsite\Excel\Facades\Excel;

class PranotaUangMakanController extends Controller
{
    public function index()
    {
        $pranotas = PranotaUangMakan::with('details')
            ->whereNull('pranota_puml_id')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pranota-uang-makan.index', compact('pranotas'));
    }

    public function show(Request $request, $id)
    {
        $pranota = PranotaUangMakan::with(['details.karyawan'])->findOrFail($id);
        
        if ($request->has('print')) {
            return view('pranota-uang-makan.print', compact('pranota'));
        }
        
        return view('pranota-uang-makan.show', compact('pranota'));
    }

    public function edit($id)
    {
        $pranota = PranotaUangMakan::with(['details.karyawan'])->findOrFail($id);

        $karyawanTetap = Karyawan::where('status', 'active')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nik', 'nama_lengkap', 'penempatan', 'cabang', 'posisi', 'nominal_uang_makan'])
            ->map(function ($k) {
                $k->unique_id = 'Karyawan_' . $k->id;
                $k->tipe_karyawan = 'App\\Models\\Karyawan';
                $k->tipe_label = 'Tetap';
                return $k;
            });

        $karyawanTidakTetap = KaryawanTidakTetap::orderBy('nama_lengkap')
            ->get(['id', 'nik', 'nama_lengkap', 'penempatan', 'cabang', 'pekerjaan'])
            ->map(function ($k) {
                $k->unique_id = 'KaryawanTidakTetap_' . $k->id;
                $k->tipe_karyawan = 'App\\Models\\KaryawanTidakTetap';
                $k->tipe_label = 'Tidak Tetap';
                $k->posisi = $k->pekerjaan ?? '-';
                $k->nominal_uang_makan = 0;
                return $k;
            });

        $allKaryawans = $karyawanTetap->concat($karyawanTidakTetap)->sortBy('nama_lengkap')->values();

        $defaultStartDate = $pranota->tanggal_pranota ? $pranota->tanggal_pranota->copy()->startOfWeek()->format('Y-m-d') : now()->startOfWeek()->format('Y-m-d');
        $defaultEndDate = $pranota->tanggal_pranota ? $pranota->tanggal_pranota->copy()->endOfWeek()->format('Y-m-d') : now()->endOfWeek()->format('Y-m-d');

        return view('pranota-uang-makan.edit', compact('pranota', 'allKaryawans', 'defaultStartDate', 'defaultEndDate'));
    }

    public function refreshAbsensi(Request $request, $id)
    {
        $pranota = PranotaUangMakan::with(['details.karyawan'])->findOrFail($id);

        $tanggal = $request->tanggal_pranota ? Carbon::parse($request->tanggal_pranota) : ($pranota->tanggal_pranota ?? now());
        $startDate = $request->start_date 
            ? Carbon::parse($request->start_date)->startOfDay() 
            : $tanggal->copy()->startOfWeek()->startOfDay();

        $endDate = $request->end_date 
            ? Carbon::parse($request->end_date)->endOfDay() 
            : $tanggal->copy()->endOfWeek()->endOfDay();

        $rowItems = $request->row_items ?? [];
        $rowMap = [];
        $tetapIds = [];
        $tidakTetapIds = [];

        if (!empty($rowItems) && is_array($rowItems)) {
            foreach ($rowItems as $item) {
                $key = $item['key'] ?? '';
                if (!$key) continue;
                $rowMap[$key] = $item;
                $parts = explode('_', $key);
                if (count($parts) > 1) {
                    if ($parts[0] === 'Karyawan') {
                        $tetapIds[] = $parts[1];
                    } elseif ($parts[0] === 'KaryawanTidakTetap') {
                        $tidakTetapIds[] = $parts[1];
                    }
                }
            }
        } else {
            foreach ($pranota->details as $d) {
                $key = class_basename($d->tipe_karyawan) . '_' . $d->karyawan_id;
                $rowMap[$key] = [
                    'key' => $key,
                    'kehadiran' => $d->kehadiran,
                    'nominal_awal' => $d->nominal_awal,
                ];
                if ($d->tipe_karyawan === 'App\\Models\\KaryawanTidakTetap') {
                    $tidakTetapIds[] = $d->karyawan_id;
                } else {
                    $tetapIds[] = $d->karyawan_id;
                }
            }
        }

        $allEmployees = collect();

        if (!empty($tetapIds)) {
            $tetap = Karyawan::whereIn('id', array_unique($tetapIds))
                ->with(['absensi' => function($q) use ($startDate, $endDate) {
                    $q->whereBetween('waktu', [$startDate, $endDate])
                      ->where('tipe', 'Masuk');
                }, 'uangMakanTerbaru'])
                ->get();
            $allEmployees = $allEmployees->merge($tetap);
        }

        if (!empty($tidakTetapIds)) {
            $tidakTetap = KaryawanTidakTetap::whereIn('id', array_unique($tidakTetapIds))
                ->with(['absensi' => function($q) use ($startDate, $endDate) {
                    $q->whereBetween('waktu', [$startDate, $endDate])
                      ->where('tipe', 'Masuk');
                }, 'uangMakanTerbaru'])
                ->get();
            $allEmployees = $allEmployees->merge($tidakTetap);
        }

        $results = [];
        $changedCount = 0;

        foreach ($allEmployees as $k) {
            $key = class_basename($k) . '_' . $k->id;

            $isSatpam = false;
            $isSatpamPelabuhan = false;
            if ($k instanceof Karyawan) {
                $kGrup = is_string($k->grup) ? json_decode($k->grup, true) : (array)$k->grup;
            } else {
                $kGrup = is_string($k->group) ? json_decode($k->group, true) : (array)$k->group;
            }
            if (is_array($kGrup)) {
                foreach ($kGrup as $g) {
                    if (stripos($g, 'SATPAM GARASI') !== false) {
                        $isSatpam = true;
                    }
                    if (stripos($g, 'SATPAM PELABUHAN') !== false) {
                        $isSatpam = true;
                        $isSatpamPelabuhan = true;
                    }
                }
            }

            // Count unique days clocked in
            $uniqueDaysDates = $k->absensi->filter(function($abs) use ($isSatpam) {
                if ($isSatpam) return true;
                return !Carbon::parse($abs->waktu)->isSunday();
            })->map(function($abs) {
                return Carbon::parse($abs->waktu)->format('Y-m-d');
            })->unique()->values();

            $uniqueDays = $uniqueDaysDates->count();

            // Multiplier
            $multiplier = 1;
            if (strcasecmp(trim($k->penempatan ?? ''), 'Pelabuhan 1') === 0 || ($k->penempatan ?? '') == '1') {
                $multiplier = 2;
            }

            // Nominal dasar per hari
            $nominalPerHari = $k->uangMakanTerbaru ? $k->uangMakanTerbaru->nominal : ($k->nominal_uang_makan ?? 0);

            // If not found in model, fallback to existing row rate
            if ($nominalPerHari <= 0 && isset($rowMap[$key])) {
                $oldNominal = (int) str_replace(['.', ',', ' '], '', $rowMap[$key]['nominal_awal'] ?? 0);
                preg_match('/\d+/', $rowMap[$key]['kehadiran'] ?? '', $matches);
                $oldDays = isset($matches[0]) ? (int)$matches[0] : 0;
                if ($oldDays > 0) {
                    $nominalPerHari = round($oldNominal / ($oldDays * $multiplier));
                }
            }

            if ($isSatpamPelabuhan) {
                $totalPayout = $multiplier * $nominalPerHari;
            } else {
                $totalPayout = $uniqueDays * $multiplier * $nominalPerHari;
            }

            $newKehadiranStr = $uniqueDays . ' Hari';
            
            // Compare with old kehadiran
            $oldKehadiranStr = isset($rowMap[$key]) ? trim($rowMap[$key]['kehadiran'] ?? '') : '';
            preg_match('/\d+/', $oldKehadiranStr, $oldMatches);
            $oldDaysNum = isset($oldMatches[0]) ? (int)$oldMatches[0] : -1;

            $isChanged = ($oldDaysNum !== $uniqueDays);
            if ($isChanged) {
                $changedCount++;
            }

            $results[$key] = [
                'kehadiran' => $newKehadiranStr,
                'kehadiran_num' => $uniqueDays,
                'old_kehadiran' => $oldKehadiranStr,
                'nominal_awal' => $totalPayout,
                'nominal_per_hari' => $nominalPerHari,
                'multiplier' => $multiplier,
                'is_changed' => $isChanged,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Data absensi berhasil disinkronkan.',
            'changed_count' => $changedCount,
            'periode' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'start_formatted' => $startDate->format('d/m/Y'),
                'end_formatted' => $endDate->format('d/m/Y'),
            ],
            'data' => $results,
        ]);
    }

    public function update(Request $request, $id)
    {
        $pranota = PranotaUangMakan::findOrFail($id);

        $request->validate([
            'nomor_pranota' => 'required|string|unique:pranota_uang_makans,nomor_pranota,' . $pranota->id,
            'tanggal_pranota' => 'required|date',
            'status' => 'nullable|string',
            'karyawans' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $pranota->update([
                'nomor_pranota' => $request->nomor_pranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'status' => $request->status ?? $pranota->status ?? 'draft',
            ]);

            // Re-sync details
            $pranota->details()->delete();

            $totalNominal = 0;

            foreach ($request->karyawans as $karyawanKey => $data) {
                $tipeKaryawan = $data['tipe_karyawan'] ?? null;
                $karyawanId = $data['karyawan_id'] ?? null;

                if (!$tipeKaryawan || !$karyawanId) {
                    $parts = explode('_', $karyawanKey);
                    $tipeKaryawan = count($parts) > 1 ? 'App\\Models\\' . $parts[0] : 'App\\Models\\Karyawan';
                    $karyawanId = count($parts) > 1 ? $parts[1] : $karyawanKey;
                }

                $nominalAwal = isset($data['nominal_awal']) ? (int) str_replace(['.', ',', ' '], '', $data['nominal_awal']) : 0;
                $adjustment = isset($data['adjustment']) ? (int) str_replace(['.', ',', ' '], '', $data['adjustment']) : 0;
                $totalAkhir = $nominalAwal + $adjustment;

                $pranota->details()->create([
                    'tipe_karyawan' => $tipeKaryawan,
                    'karyawan_id' => $karyawanId,
                    'kehadiran' => $data['kehadiran'] ?? null,
                    'nominal_awal' => $nominalAwal,
                    'adjustment' => $adjustment,
                    'total_akhir' => $totalAkhir,
                    'catatan' => $data['catatan'] ?? null,
                ]);

                $totalNominal += $totalAkhir;
            }

            $pranota->update(['total_nominal' => $totalNominal]);

            DB::commit();

            return redirect()->route('pranota-uang-makan.index')->with('success', 'Pranota Uang Makan ' . $pranota->nomor_pranota . ' berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui Pranota: ' . $e->getMessage())->withInput();
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_pranota' => 'required|string|unique:pranota_uang_makans,nomor_pranota',
            'tanggal_pranota' => 'required|date',
            'karyawans' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $pranota = PranotaUangMakan::create([
                'nomor_pranota' => $request->nomor_pranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'total_nominal' => 0, // Will calculate below
                'status' => 'draft',
            ]);

            $totalNominal = 0;

            foreach ($request->karyawans as $karyawanKey => $data) {
                // Parse key like "Karyawan_257" or "KaryawanTidakTetap_12"
                $parts = explode('_', $karyawanKey);
                $tipeKaryawan = count($parts) > 1 ? 'App\\Models\\' . $parts[0] : 'App\\Models\\Karyawan';
                $karyawanId = count($parts) > 1 ? $parts[1] : $karyawanKey;

                // Determine the total akhir based on inputs
                $nominalAwal = isset($data['nominal_awal']) ? (int) str_replace(['.', ',', ' '], '', $data['nominal_awal']) : 0;
                $adjustment = isset($data['adjustment']) ? (int) str_replace(['.', ',', ' '], '', $data['adjustment']) : 0;
                $totalAkhir = $nominalAwal + $adjustment;
                
                $pranota->details()->create([
                    'tipe_karyawan' => $tipeKaryawan,
                    'karyawan_id' => $karyawanId,
                    'kehadiran' => $data['kehadiran'] ?? null,
                    'nominal_awal' => $nominalAwal,
                    'adjustment' => $adjustment,
                    'total_akhir' => $totalAkhir,
                    'catatan' => $data['catatan'] ?? null,
                ]);

                $totalNominal += $totalAkhir;
            }

            $pranota->update(['total_nominal' => $totalNominal]);

            DB::commit();

            return redirect()->route('pranota-uang-makan.index')->with('success', 'Pranota Uang Makan berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan Pranota: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $pranota = PranotaUangMakan::findOrFail($id);
            $pranota->delete(); // Details will cascade
            return redirect()->back()->with('success', 'Pranota Uang Makan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus Pranota: ' . $e->getMessage());
        }
    }

    public function exportAutoTransfer($id)
    {
        $pranota = PranotaUangMakan::with(['details.karyawan'])->findOrFail($id);
        $filename = 'Auto_Transfer_Uang_Makan_' . str_replace('/', '_', $pranota->nomor_pranota) . '.xlsx';
        return Excel::download(new PranotaUangMakanAutoTransferExport($pranota), $filename);
    }
}
