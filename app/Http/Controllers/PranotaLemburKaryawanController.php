<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PranotaLemburKaryawanController extends Controller
{
    public function employeeCandidates(Request $request, $id, \App\Services\PranotaLemburEmployeeService $service)
    {
        $pranota = \App\Models\PranotaLemburKaryawanHeader::with('pranotaPuml')->findOrFail($id);
        abort_if(in_array(optional($pranota->pranotaPuml)->status, ['approved', 'paid']), 403, 'PUML sudah disetujui atau dibayar.');
        [$start, $end] = $this->employeeSelectionPeriod($request, $pranota, $service);
        [$periodStart, $periodEnd] = $service->period($pranota);

        return response()->json([
            'nomor_pranota' => $pranota->nomor_pranota,
            'periode' => $start->format('d/m/Y').' – '.$end->format('d/m/Y'),
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'periode_mulai' => $periodStart->toDateString(),
            'periode_selesai' => $periodEnd->toDateString(),
            'periode_setelah_penambahan' => $periodStart->min($start)->format('d/m/Y').' – '.$periodEnd->max($end)->format('d/m/Y'),
            'periode_berubah' => $start->lt($periodStart) || $end->gt($periodEnd),
            'data' => $service->candidates($pranota, null, $start, $end),
        ]);
    }

    public function addEmployees(Request $request, $id, \App\Services\PranotaLemburEmployeeService $service)
    {
        $validated = $request->validate([
            'karyawan_ids' => 'required|array|min:1',
            'karyawan_ids.*' => 'required|integer|distinct|exists:karyawans,id',
        ]);

        $count = \DB::transaction(function () use ($request, $id, $validated, $service) {
            $pranota = \App\Models\PranotaLemburKaryawanHeader::lockForUpdate()->findOrFail($id);
            $puml = $pranota->pranota_puml_id
                ? \App\Models\PranotaPuml::lockForUpdate()->find($pranota->pranota_puml_id) : null;
            abort_if($puml && in_array($puml->status, ['approved', 'paid']), 403, 'PUML sudah disetujui atau dibayar.');
            [$start, $end] = $this->employeeSelectionPeriod($request, $pranota, $service);
            // Serialize additions of the same employee even across different pranotas.
            \App\Models\Karyawan::whereIn('id', $validated['karyawan_ids'])->orderBy('id')->lockForUpdate()->get();
            $candidates = $service->candidates($pranota, $validated['karyawan_ids'], $start, $end);
            if ($candidates->count() !== count($validated['karyawan_ids'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'karyawan_ids' => 'Pilihan tidak lagi tersedia. Karyawan sudah masuk pranota atau tidak memiliki lembur yang belum dipranotakan pada periode ini. Muat ulang pilihan karyawan.',
                ]);
            }
            [$periodStart, $periodEnd] = $service->period($pranota);
            // Preserve the bounds of legacy employee rows before expanding the header.
            if ($start->lt($periodStart) || $end->gt($periodEnd)) {
                $pranota->karyawans()->whereNull('periode_mulai')->update(['periode_mulai' => $periodStart]);
                $pranota->karyawans()->whereNull('periode_selesai')->update(['periode_selesai' => $periodEnd]);
            }
            foreach ($candidates as $employee) {
                $pranota->karyawans()->create([
                    'karyawan_id' => $employee['karyawan_id'],
                    'periode_mulai' => $start,
                    'periode_selesai' => $end,
                    'tanggal_lembur' => $employee['tanggal_lembur'],
                    'jam_lembur' => $employee['jam_lembur'],
                    'nominal_awal' => $employee['nominal_awal'],
                    'adjustment' => 0,
                    'total_akhir' => $employee['nominal_awal'],
                ]);
            }
            $totals = $pranota->karyawans()->selectRaw('SUM(nominal_awal) as biaya, SUM(adjustment) as adj, SUM(total_akhir) as akhir')->first();
            $pranota->update([
                'periode_mulai' => $periodStart->min($start),
                'periode_selesai' => $periodEnd->max($end),
                'total_biaya' => $totals->biaya,
                'adjustment' => $totals->adj,
                'total_setelah_adjustment' => $totals->akhir,
                'updated_by' => auth()->id(),
            ]);
            if ($puml) {
                $sum = $puml->lemburs()->sum('total_setelah_adjustment');
                $puml->update(['total_lembur' => $sum, 'grand_total' => $puml->total_uang_makan + $sum]);
            }
            return $candidates->count();
        });
        $request->session()->flash('success', $count.' karyawan berhasil ditambahkan ke pranota lembur.');
        $request->session()->flash('open_riwayat', true);

        return response()->json(['message' => $count.' karyawan berhasil ditambahkan ke pranota lembur.']);
    }

    private function employeeSelectionPeriod(Request $request, \App\Models\PranotaLemburKaryawanHeader $pranota, \App\Services\PranotaLemburEmployeeService $service): array
    {
        $dates = $request->validate([
            'start_date' => 'nullable|required_with:end_date|date_format:Y-m-d',
            'end_date' => 'nullable|required_with:start_date|date_format:Y-m-d|after_or_equal:start_date',
        ]);
        [$periodStart, $periodEnd] = $service->period($pranota);
        $start = isset($dates['start_date']) ? \Carbon\Carbon::parse($dates['start_date']) : $periodStart;
        $end = isset($dates['end_date']) ? \Carbon\Carbon::parse($dates['end_date']) : $periodEnd;

        return [$start, $end];
    }

    public function index(Request $request)
    {
        $query = \App\Models\PranotaLemburKaryawanHeader::query()
            ->whereNull('pranota_puml_id');

        if ($request->filled('nomor_pranota')) {
            $query->where('nomor_pranota', 'like', '%'.$request->nomor_pranota.'%');
        }

        if ($request->filled('tanggal_pranota')) {
            $query->where('tanggal_pranota', $request->tanggal_pranota);
        }

        if ($request->filled('only_my') && $request->only_my == '1') {
            $query->where('created_by', auth()->id());
        }

        $pranotas = $query->with(['creator', 'karyawans'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('pranota-lembur-karyawan.index', compact('pranotas'));
    }

    public function show($id)
    {
        $pranota = \App\Models\PranotaLemburKaryawanHeader::with(['karyawans.karyawan'])
            ->findOrFail($id);

        return view('pranota-lembur-karyawan.show', compact('pranota'));
    }

    public function edit($id)
    {
        $pranota = \App\Models\PranotaLemburKaryawanHeader::with(['karyawans.karyawan', 'pranotaPuml'])
            ->findOrFail($id);

        if ($pranota->pranota_puml_id && in_array(optional($pranota->pranotaPuml)->status, ['approved', 'paid'])) {
            return redirect()->route('pranota-lembur-karyawan.show', $id)
                ->with('error', 'Pranota tidak dapat diedit karena PUML induk sudah ' . $pranota->pranotaPuml->status . '.');
        }

        return view('pranota-lembur-karyawan.edit', compact('pranota'));
    }

    public function export($id)
    {
        $pranota = \App\Models\PranotaLemburKaryawanHeader::with(['creator', 'karyawans.karyawan'])
            ->findOrFail($id);

        $safeNomor = preg_replace('/[^A-Za-z0-9_\-]/', '_', $pranota->nomor_pranota);
        $filename = 'Pranota_Lembur_'.$safeNomor.'.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PranotaLemburKaryawanExport($pranota), $filename);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pranota' => 'required|date',
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date',
            'karyawans' => 'required|array',
            'karyawans.*.kehadiran' => 'required|string',
            'karyawans.*.nominal_awal' => 'required|numeric',
            'karyawans.*.adjustment' => 'nullable|numeric',
            'karyawans.*.nominal_lembur' => 'nullable|numeric',
            'karyawans.*.uang_makan_lembur' => 'nullable|numeric',
            'karyawans.*.nominal_per_hari' => 'nullable|numeric',
            'karyawans.*.catatan' => 'nullable|string',
            'karyawans.*.tanggal_lembur' => 'nullable',
        ]);

        $periodeMulai = $validated['periode_mulai'] ?? null;
        $periodeSelesai = $validated['periode_selesai'] ?? null;

        // Fallback periode dari tanggal_pranota jika kosong
        if (! $periodeMulai || ! $periodeSelesai) {
            $parsedDate = \Carbon\Carbon::parse($validated['tanggal_pranota']);
            $periodeMulai = $periodeMulai ?: $parsedDate->copy()->startOfMonth()->toDateString();
            $periodeSelesai = $periodeSelesai ?: $parsedDate->copy()->endOfMonth()->toDateString();
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Generate nomor pranota
            $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'PML')->first();
            if (! $nomorTerakhir) {
                $nomorTerakhir = \App\Models\NomorTerakhir::create([
                    'modul' => 'PML',
                    'nomor_terakhir' => 0,
                ]);
            }
            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $tahun = now()->format('y');
            $bulan = now()->format('m');
            $nomorCetakan = 1; // Default
            $nomorPranota = "PML{$nomorCetakan}{$bulan}{$tahun}".str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $totalBiaya = 0;
            $totalAdjustment = 0;

            foreach ($validated['karyawans'] as $karyawanId => $data) {
                $nominalAwal = $data['nominal_awal'] ?? 0;
                $adj = $data['adjustment'] ?? 0;
                $totalBiaya += $nominalAwal;
                $totalAdjustment += $adj;
            }

            $totalSetelahAdjustment = $totalBiaya + $totalAdjustment;

            // Save parent
            $pranota = \App\Models\PranotaLemburKaryawanHeader::create([
                'nomor_pranota' => $nomorPranota,
                'nomor_cetakan' => $nomorCetakan,
                'tanggal_pranota' => $validated['tanggal_pranota'],
                'periode_mulai' => $periodeMulai,
                'periode_selesai' => $periodeSelesai,
                'total_biaya' => $totalBiaya,
                'adjustment' => $totalAdjustment,
                'total_setelah_adjustment' => $totalSetelahAdjustment,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Save details
            foreach ($validated['karyawans'] as $karyawanId => $data) {
                $nominalAwal = $data['nominal_awal'] ?? 0;
                $adj = $data['adjustment'] ?? 0;
                $totalAkhir = $nominalAwal + $adj;

                $tanggalLembur = null;
                if (! empty($data['tanggal_lembur'])) {
                    $tanggalLembur = is_array($data['tanggal_lembur'])
                        ? $data['tanggal_lembur']
                        : json_decode($data['tanggal_lembur'], true);
                }

                \App\Models\PranotaLemburKaryawan::create([
                    'pranota_lembur_karyawan_header_id' => $pranota->id,
                    'karyawan_id' => $karyawanId,
                    'periode_mulai' => $periodeMulai,
                    'periode_selesai' => $periodeSelesai,
                    'tanggal_lembur' => $tanggalLembur,
                    'jam_lembur' => $data['kehadiran'],
                    'nominal_awal' => $nominalAwal,
                    'adjustment' => $adj,
                    'total_akhir' => $totalAkhir,
                    'catatan' => $data['catatan'] ?? null,
                ]);
            }

            $nomorTerakhir->update(['nomor_terakhir' => $nextNumber]);

            \Illuminate\Support\Facades\DB::commit();

            return back()->with('success', 'Pranota Lembur berhasil disimpan dengan nomor: '.$nomorPranota);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();

            return back()->with('error', 'Gagal menyimpan Pranota Lembur: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $pranota = \App\Models\PranotaLemburKaryawanHeader::findOrFail($id);

        if ($pranota->pranota_puml_id) {
            $puml = \App\Models\PranotaPuml::find($pranota->pranota_puml_id);
            if ($puml && in_array($puml->status, ['approved', 'paid'])) {
                return back()->with('error', 'Pranota tidak dapat diedit karena PUML induk sudah '.$puml->status.'.');
            }
        }

        $validated = $request->validate([
            'tanggal_pranota' => 'required|date',
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date',
            'karyawans' => 'required|array|min:1',
            'karyawans.*.detail_id' => 'nullable',
            'karyawans.*.karyawan_id' => 'required|integer',
            'karyawans.*.jam_lembur' => 'nullable|string',
            'karyawans.*.nominal_awal' => 'required|numeric',
            'karyawans.*.adjustment' => 'nullable|numeric',
            'karyawans.*.catatan' => 'nullable|string',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $submittedDetailIds = [];
            $totalBiaya = 0;
            $totalAdjustment = 0;

            foreach ($validated['karyawans'] as $itemData) {
                $detailId = $itemData['detail_id'] ?? null;
                $nominalAwal = (float) ($itemData['nominal_awal'] ?? 0);
                $adj = (float) ($itemData['adjustment'] ?? 0);
                $totalAkhir = $nominalAwal + $adj;

                $totalBiaya += $nominalAwal;
                $totalAdjustment += $adj;

                if ($detailId) {
                    $detail = \App\Models\PranotaLemburKaryawan::where('pranota_lembur_karyawan_header_id', $pranota->id)
                        ->where('id', $detailId)
                        ->first();
                    if ($detail) {
                        $detail->update([
                            'jam_lembur' => $itemData['jam_lembur'] ?? $detail->jam_lembur,
                            'nominal_awal' => $nominalAwal,
                            'adjustment' => $adj,
                            'total_akhir' => $totalAkhir,
                            'catatan' => $itemData['catatan'] ?? null,
                        ]);
                        $submittedDetailIds[] = $detail->id;
                    }
                } else {
                    $newDetail = \App\Models\PranotaLemburKaryawan::create([
                        'pranota_lembur_karyawan_header_id' => $pranota->id,
                        'karyawan_id' => $itemData['karyawan_id'],
                        'periode_mulai' => $validated['periode_mulai'] ?? $pranota->periode_mulai,
                        'periode_selesai' => $validated['periode_selesai'] ?? $pranota->periode_selesai,
                        'jam_lembur' => $itemData['jam_lembur'] ?? '0 Jam',
                        'nominal_awal' => $nominalAwal,
                        'adjustment' => $adj,
                        'total_akhir' => $totalAkhir,
                        'catatan' => $itemData['catatan'] ?? null,
                    ]);
                    $submittedDetailIds[] = $newDetail->id;
                }
            }

            // Hapus karyawan yang dikeluarkan dari pranota
            if (!empty($submittedDetailIds)) {
                \App\Models\PranotaLemburKaryawan::where('pranota_lembur_karyawan_header_id', $pranota->id)
                    ->whereNotIn('id', $submittedDetailIds)
                    ->delete();
            }

            $totalSetelahAdjustment = $totalBiaya + $totalAdjustment;

            $pranota->update([
                'tanggal_pranota' => $validated['tanggal_pranota'],
                'periode_mulai' => $validated['periode_mulai'] ?? $pranota->periode_mulai,
                'periode_selesai' => $validated['periode_selesai'] ?? $pranota->periode_selesai,
                'total_biaya' => $totalBiaya,
                'adjustment' => $totalAdjustment,
                'total_setelah_adjustment' => $totalSetelahAdjustment,
                'updated_by' => auth()->id(),
            ]);

            // Sync PUML jika terhubung
            if ($pranota->pranota_puml_id) {
                $puml = \App\Models\PranotaPuml::find($pranota->pranota_puml_id);
                if ($puml) {
                    $sumLembur = \App\Models\PranotaLemburKaryawanHeader::where('pranota_puml_id', $puml->id)->sum('total_setelah_adjustment');
                    $puml->update([
                        'total_lembur' => $sumLembur,
                        'grand_total' => $puml->total_uang_makan + $sumLembur,
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            if ($request->input('_redirect_to') === 'show') {
                return redirect()->route('pranota-lembur-karyawan.show', $pranota->id)
                    ->with('success', 'Pranota lembur '.$pranota->nomor_pranota.' berhasil diperbarui.');
            }

            return redirect()->back()
                ->with('success', 'Pranota lembur '.$pranota->nomor_pranota.' berhasil diperbarui.')
                ->with('open_riwayat', true);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui pranota lembur: '.$e->getMessage())
                ->with('open_riwayat', true);
        }
    }

    public function refreshAttendance(Request $request, $id)
    {
        $pranota = \App\Models\PranotaLemburKaryawanHeader::with(['karyawans.karyawan'])->findOrFail($id);

        $startDateStr = $pranota->periode_mulai ? $pranota->periode_mulai->toDateString() : $pranota->tanggal_pranota->copy()->startOfMonth()->toDateString();
        $endDateStr = $pranota->periode_selesai ? $pranota->periode_selesai->toDateString() : $pranota->tanggal_pranota->copy()->endOfMonth()->toDateString();

        $karyawanIds = $pranota->karyawans->pluck('karyawan_id')->unique()->filter()->values()->toArray();

        // A newly added employee may reserve only part of the period. Refresh
        // those dates without reintroducing dates already billed elsewhere.
        $excludedDates = [];
        foreach ($pranota->karyawans as $detail) {
            $allowed = is_array($detail->tanggal_lembur) && ! empty($detail->tanggal_lembur)
                ? array_fill_keys($detail->tanggal_lembur, true) : null;
            $detailStart = $detail->periode_mulai ?? \Carbon\Carbon::parse($startDateStr);
            $detailEnd = $detail->periode_selesai ?? \Carbon\Carbon::parse($endDateStr);
            $end = \Carbon\Carbon::parse($endDateStr);
            for ($day = \Carbon\Carbon::parse($startDateStr); $day->lte($end); $day->addDay()) {
                if ($day->lt($detailStart) || $day->gt($detailEnd) || ($allowed !== null && ! isset($allowed[$day->toDateString()]))) {
                    $excludedDates[$detail->karyawan_id][$day->toDateString()] = true;
                }
            }
        }
        $recalculated = \App\Http\Controllers\PerhitunganLemburController::calculateAttendanceForKaryawans(
            $karyawanIds,
            $startDateStr,
            $endDateStr,
            $excludedDates
        );

        $results = [];
        foreach ($pranota->karyawans as $detail) {
            $kId = $detail->karyawan_id;
            $calc = $recalculated[$kId] ?? null;

            if ($calc) {
                $results[] = [
                    'detail_id' => $detail->id,
                    'karyawan_id' => $kId,
                    'jam_lembur' => $calc['jam_lembur'],
                    'total_jam' => $calc['total_jam'],
                    'nominal_awal' => $calc['nominal_awal'],
                    'nominal_lembur' => $calc['nominal_lembur'],
                    'uang_makan_lembur' => $calc['uang_makan_lembur'],
                    'total_hari' => $calc['total_hari'],
                    'tanggal_lembur' => $calc['tanggal_lembur'],
                ];
            } else {
                $results[] = [
                    'detail_id' => $detail->id,
                    'karyawan_id' => $kId,
                    'jam_lembur' => '0 Jam',
                    'total_jam' => 0,
                    'nominal_awal' => 0,
                    'nominal_lembur' => 0,
                    'uang_makan_lembur' => 0,
                    'total_hari' => 0,
                    'tanggal_lembur' => [],
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menyinkronkan data absensi untuk '.count($results).' karyawan.',
            'periode' => $startDateStr.' s/d '.$endDateStr,
            'data' => $results,
        ]);
    }

    public function destroy($id)
    {
        $pranota = \App\Models\PranotaLemburKaryawanHeader::findOrFail($id);

        if ($pranota->pranota_puml_id) {
            $puml = \App\Models\PranotaPuml::find($pranota->pranota_puml_id);
            if ($puml && in_array($puml->status, ['approved', 'paid'])) {
                return back()->with('error', 'Pranota tidak dapat dihapus karena PUML induk sudah '.$puml->status.'.');
            }

            if ($puml) {
                $puml->update([
                    'total_lembur' => max(0, $puml->total_lembur - $pranota->total_setelah_adjustment),
                    'grand_total' => max(0, $puml->grand_total - $pranota->total_setelah_adjustment),
                ]);
            }
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            \App\Models\PranotaLemburKaryawan::where('pranota_lembur_karyawan_header_id', $pranota->id)->delete();
            $pranota->delete();

            \Illuminate\Support\Facades\DB::commit();

            return back()->with('success', 'Pranota lembur '.$pranota->nomor_pranota.' berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();

            return back()->with('error', 'Gagal menghapus pranota: '.$e->getMessage());
        }
    }
}
