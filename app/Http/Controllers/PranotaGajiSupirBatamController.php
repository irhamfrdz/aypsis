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

        return view('pranota-gaji-supir-batam.print', ['pranota' => $pranotaGajiSupirBatam]);
    }

    public function destroy(PranotaGajiSupirBatam $pranotaGajiSupirBatam)
    {
        $pranotaGajiSupirBatam->delete();

        return redirect()->route('pranota-gaji-supir-batam.index')
            ->with('success', 'Pranota dihapus. Gaji yang belum dibayar dapat dipilih kembali.');
    }
}
