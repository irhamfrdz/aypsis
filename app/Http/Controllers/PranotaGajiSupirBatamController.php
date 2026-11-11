<?php

namespace App\Http\Controllers;

use App\Models\GajiSupirBatam;
use App\Models\PranotaGajiSupirBatam;
use App\Models\PranotaGajiSupirBatamItem;
use App\Services\SupirBatamBpjsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PranotaGajiSupirBatamController extends Controller
{
    public function index()
    {
        $pranotas = PranotaGajiSupirBatam::withCount('items')
            ->with('items.gaji:id,tanggal_mulai,tanggal_selesai')
            ->latest('tanggal_pranota')->latest('id')->paginate(20);

        return view('pranota-gaji-supir-batam.index', compact('pranotas'));
    }

    public function create()
    {
        $gajiList = GajiSupirBatam::with('karyawan')
            ->where('status_pembayaran', 'PENDING')
            ->whereDoesntHave('pranotaItem')
            ->orderBy('tanggal_mulai')->orderBy('id')->get();

        $gajiGroups = $gajiList->groupBy(fn ($gaji) => implode('|', [
            $gaji->tanggal_mulai?->toDateString() ?? '',
            $gaji->periode_bulan,
            $gaji->tanggal_selesai?->toDateString() ?? '',
        ]));

        $bpjsByEmployee = app(SupirBatamBpjsService::class)->latestForEmployees($gajiList->pluck('karyawan'));

        return view('pranota-gaji-supir-batam.create', compact('gajiList', 'gajiGroups', 'bpjsByEmployee'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pranota' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'gaji_ids' => ['required', 'array', 'min:1'],
            'gaji_ids.*' => ['required', 'integer', 'distinct', 'exists:gaji_supir_batams,id'],
            'pph' => ['required', 'array'],
            'pph.*' => ['required', 'numeric', 'min:0', 'max:9999999999999.99', 'decimal:0,2'],
        ]);

        $pranota = DB::transaction(function () use ($validated, $request) {
            $gajiList = GajiSupirBatam::with('karyawan')->whereKey($validated['gaji_ids'])
                ->orderBy('id')->lockForUpdate()->get();
            if ($gajiList->count() !== count($validated['gaji_ids'])
                || $gajiList->contains(fn ($gaji) => $gaji->status_pembayaran !== 'PENDING')
                || PranotaGajiSupirBatamItem::whereIn('gaji_supir_batam_id', $validated['gaji_ids'])->exists()) {
                throw ValidationException::withMessages([
                    'gaji_ids' => 'Ada gaji yang sudah dibayar, dibatalkan, atau sudah masuk pranota. Muat ulang daftar gaji.',
                ]);
            }

            $bpjsByEmployee = app(SupirBatamBpjsService::class)->latestForEmployees($gajiList->pluck('karyawan'));
            $remainingBpjs = [];
            $remainingPph = [];
            foreach ($gajiList->groupBy('karyawan_id') as $employeeId => $salaries) {
                $pph = $validated['pph'][$employeeId] ?? null;
                $bpjs = (int) round(($bpjsByEmployee[$employeeId]['amount'] ?? 0) * 100);
                if ($pph === null || $bpjs < 0 || round((float) $pph * 100) + $bpjs > round((float) $salaries->sum('total_gaji') * 100)) {
                    throw ValidationException::withMessages([
                        'pph.'.$employeeId => 'Isi PPh setiap supir. Total PPh dan BPJS tidak boleh melebihi gaji yang dipilih, dan nominal BPJS harus valid.',
                    ]);
                }
                $remainingPph[$employeeId] = (int) round((float) $pph * 100);
                $remainingBpjs[$employeeId] = $bpjs;
            }

            $pranota = PranotaGajiSupirBatam::create([
                'nomor_pranota' => 'TEMP-'.(string) \Illuminate\Support\Str::uuid(),
                'tanggal_pranota' => $validated['tanggal_pranota'],
                'total_gaji' => ($gajiList->sum(fn ($gaji) => (int) round((float) $gaji->total_gaji * 100)) - array_sum($remainingPph) - array_sum($remainingBpjs)) / 100,
                'keterangan' => $validated['keterangan'] ?? null,
                'created_by' => $request->user()?->id,
            ]);
            $pranota->update(['nomor_pranota' => sprintf('PGSB/%s/%05d', $pranota->tanggal_pranota->format('Y'), $pranota->id)]);

            foreach ($gajiList as $gaji) {
                // Allocate one deduction per driver across their selected periods.
                $salaryCents = (int) round((float) $gaji->total_gaji * 100);
                $bpjsCents = min($remainingBpjs[$gaji->karyawan_id], max(0, $salaryCents));
                $remainingBpjs[$gaji->karyawan_id] -= $bpjsCents;
                $pphCents = min($remainingPph[$gaji->karyawan_id], max(0, $salaryCents - $bpjsCents));
                $remainingPph[$gaji->karyawan_id] -= $pphCents;
                $pranota->items()->create([
                    'gaji_supir_batam_id' => $gaji->id,
                    'total_gaji' => ($salaryCents - $pphCents - $bpjsCents) / 100,
                    'pph' => $pphCents / 100,
                    'bpjs' => $bpjsCents / 100,
                    'bpjs_detail_id' => $bpjsByEmployee[$gaji->karyawan_id]['detail_id'] ?? null,
                    'bpjs_nomor_pranota' => $bpjsByEmployee[$gaji->karyawan_id]['number'] ?? null,
                ]);
            }

            return $pranota;
        });

        return redirect()->route('pranota-gaji-supir-batam.show', $pranota)
            ->with('success', 'Pranota gaji supir berhasil dibuat.');
    }

    public function show(PranotaGajiSupirBatam $pranotaGajiSupirBatam)
    {
        $pranotaGajiSupirBatam->load('items.gaji.karyawan', 'creator');
        return view('pranota-gaji-supir-batam.show', ['pranota' => $pranotaGajiSupirBatam]);
    }

    public function print(PranotaGajiSupirBatam $pranotaGajiSupirBatam)
    {
        $pranotaGajiSupirBatam->load('items.gaji.karyawan', 'creator');
        $tanggalMulai = $pranotaGajiSupirBatam->items
            ->pluck('gaji.tanggal_mulai')->filter()->sort()->first();
        $tanggalSelesai = $pranotaGajiSupirBatam->items
            ->pluck('gaji.tanggal_selesai')->filter()->sort()->last();
        $drivers = $pranotaGajiSupirBatam->items
            ->groupBy(fn ($item) => $item->gaji?->karyawan_id ?? 'missing-'.$item->gaji_supir_batam_id)
            ->map(function ($items) {
                $first = $items->first();
                $gaji = $first->gaji;

                return (object) [
                    'nik' => $gaji?->karyawan?->nik,
                    'nama' => $gaji?->karyawan?->nama_lengkap ?: 'Supir tidak tersedia',
                    'gaji_pokok' => $items->sum(fn ($item) => (float) ($item->gaji?->gaji_pokok ?? 0)),
                    'uang_malam_libur' => $items->sum(fn ($item) => (float) ($item->gaji?->uang_malam_libur ?? 0)),
                    'biaya_bensin' => $items->sum(fn ($item) => (float) ($item->gaji?->biaya_bensin ?? 0)),
                    'has_potongan_5_persen' => $items->contains(fn ($item) => (bool) ($item->gaji?->is_potongan_5_persen ?? false)),
                    'potongan_5_persen' => $items->sum(fn ($item) => ($item->gaji?->is_potongan_5_persen ?? false)
                        ? (float) ($item->gaji?->nominal_potongan_5_persen ?? 0) : 0),
                    'total_gaji' => $items->sum(fn ($item) => (float) $item->total_gaji),
                    'pph' => $items->sum(fn ($item) => (float) $item->pph),
                    'bpjs' => $items->sum(fn ($item) => (float) $item->bpjs),
                ];
            })->values();
        $showPotongan = $drivers->contains(fn ($driver) => $driver->has_potongan_5_persen);

        return view('pranota-gaji-supir-batam.print', [
            'pranota' => $pranotaGajiSupirBatam,
            'drivers' => $drivers,
            'showPotongan' => $showPotongan,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
        ]);
    }

    public function destroy(PranotaGajiSupirBatam $pranotaGajiSupirBatam)
    {
        $pranotaGajiSupirBatam->delete();

        return redirect()->route('pranota-gaji-supir-batam.index')
            ->with('success', 'Pranota dihapus. Gaji yang belum dibayar dapat dipilih kembali.');
    }
}
