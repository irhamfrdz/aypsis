<?php

namespace App\Http\Controllers;

use App\Models\GajiSupirBatam;
use App\Models\PranotaGajiSupirBatam;
use App\Models\PranotaGajiSupirBatamItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PranotaGajiSupirBatamController extends Controller
{
    public function index()
    {
        $pranotas = PranotaGajiSupirBatam::withCount('items')
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

        return view('pranota-gaji-supir-batam.create', compact('gajiList', 'gajiGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pranota' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'gaji_ids' => ['required', 'array', 'min:1'],
            'gaji_ids.*' => ['required', 'integer', 'distinct', 'exists:gaji_supir_batams,id'],
        ]);

        $pranota = DB::transaction(function () use ($validated, $request) {
            $gajiList = GajiSupirBatam::whereKey($validated['gaji_ids'])
                ->orderBy('id')->lockForUpdate()->get();
            if ($gajiList->count() !== count($validated['gaji_ids'])
                || $gajiList->contains(fn ($gaji) => $gaji->status_pembayaran !== 'PENDING')
                || PranotaGajiSupirBatamItem::whereIn('gaji_supir_batam_id', $validated['gaji_ids'])->exists()) {
                throw ValidationException::withMessages([
                    'gaji_ids' => 'Ada gaji yang sudah dibayar, dibatalkan, atau sudah masuk pranota. Muat ulang daftar gaji.',
                ]);
            }

            $pranota = PranotaGajiSupirBatam::create([
                'nomor_pranota' => 'TEMP-'.(string) \Illuminate\Support\Str::uuid(),
                'tanggal_pranota' => $validated['tanggal_pranota'],
                'total_gaji' => $gajiList->sum(fn ($gaji) => (float) $gaji->total_gaji),
                'keterangan' => $validated['keterangan'] ?? null,
                'created_by' => $request->user()?->id,
            ]);
            $pranota->update(['nomor_pranota' => sprintf('PGSB/%s/%05d', $pranota->tanggal_pranota->format('Y'), $pranota->id)]);

            foreach ($gajiList as $gaji) {
                $pranota->items()->create([
                    'gaji_supir_batam_id' => $gaji->id,
                    'total_gaji' => $gaji->total_gaji,
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
                ];
            })->values();
        $showPotongan = $drivers->contains(fn ($driver) => $driver->has_potongan_5_persen);

        return view('pranota-gaji-supir-batam.print', [
            'pranota' => $pranotaGajiSupirBatam,
            'drivers' => $drivers,
            'showPotongan' => $showPotongan,
        ]);
    }

    public function destroy(PranotaGajiSupirBatam $pranotaGajiSupirBatam)
    {
        $pranotaGajiSupirBatam->delete();

        return redirect()->route('pranota-gaji-supir-batam.index')
            ->with('success', 'Pranota dihapus. Gaji yang belum dibayar dapat dipilih kembali.');
    }
}
