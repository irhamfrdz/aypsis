<?php

namespace App\Http\Controllers;

use App\Models\BiayaBensin;
use App\Models\MasterKartuBensinBatam;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterKartuBensinBatamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterKartuBensinBatam::with(['mobil', 'createdBy', 'updatedBy']);

        // Search filter
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_kartu', 'like', "%{$search}%")
                    ->orWhere('nama_kartu', 'like', "%{$search}%")
                    ->orWhere('provider', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('mobil', function ($mq) use ($search) {
                        $mq->where('nomor_polisi', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Statistics for summary cards
        $statsQuery = clone $query;
        $totalCards = MasterKartuBensinBatam::count();
        $activeCards = MasterKartuBensinBatam::where('status', 'aktif')->count();
        $inactiveCards = MasterKartuBensinBatam::where('status', 'tidak_aktif')->count();

        $items = $query->paginate(20)->appends($request->all());

        return view('master-kartu-bensin-batam.index', compact('items', 'totalCards', 'activeCards', 'inactiveCards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mobils = Mobil::orderBy('nomor_polisi')->get();

        return view('master-kartu-bensin-batam.create', compact('mobils'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_kartu' => 'required|string|max:255|unique:master_kartu_bensin_batams,nomor_kartu',
            'nama_kartu' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'mobil_id' => 'nullable|exists:mobils,id',
            'status' => 'required|in:aktif,tidak_aktif',
            'saldo' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $validated['saldo'] = $validated['saldo'] ?? 0;
        $validated['created_by'] = auth()->id();

        $card = MasterKartuBensinBatam::create($validated);

        if ($card->saldo > 0) {
            $card->histories()->create([
                'tanggal' => now(),
                'tipe' => 'bertambah',
                'nominal' => $card->saldo,
                'saldo_sebelum' => 0,
                'saldo_sesudah' => $card->saldo,
                'keterangan' => 'Saldo awal saat kartu didaftarkan',
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->route('master-kartu-bensin-batam.index')
            ->with('success', 'Data kartu bensin Batam berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = MasterKartuBensinBatam::findOrFail($id);
        $mobils = Mobil::orderBy('nomor_polisi')->get();

        return view('master-kartu-bensin-batam.edit', compact('item', 'mobils'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = MasterKartuBensinBatam::findOrFail($id);

        $validated = $request->validate([
            'nomor_kartu' => 'required|string|max:255|unique:master_kartu_bensin_batams,nomor_kartu,'.$item->id,
            'nama_kartu' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'mobil_id' => 'nullable|exists:mobils,id',
            'status' => 'required|in:aktif,tidak_aktif',
            'saldo' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $oldSaldo = floatval($item->saldo);
        $newSaldo = floatval($validated['saldo'] ?? 0);
        $validated['saldo'] = $newSaldo;
        $validated['updated_by'] = auth()->id();

        $item->update($validated);

        if ($newSaldo != $oldSaldo) {
            $tipe = $newSaldo > $oldSaldo ? 'bertambah' : 'berkurang';
            $nominal = abs($newSaldo - $oldSaldo);
            $item->histories()->create([
                'tanggal' => now(),
                'tipe' => $tipe,
                'nominal' => $nominal,
                'saldo_sebelum' => $oldSaldo,
                'saldo_sesudah' => $newSaldo,
                'keterangan' => 'Penyesuaian saldo kartu',
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->route('master-kartu-bensin-batam.index')
            ->with('success', 'Data kartu bensin Batam berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = MasterKartuBensinBatam::findOrFail($id);
        $item->delete();

        return redirect()->route('master-kartu-bensin-batam.index')
            ->with('success', 'Data kartu bensin Batam berhasil dihapus.');
    }

    /**
     * Display the usage history of the specified resource.
     */
    public function history($id)
    {
        $card = MasterKartuBensinBatam::findOrFail($id);
        $histories = $card->histories()->with('createdBy')->latest()->paginate(25);

        $legacyHistories = $histories->getCollection()->filter(fn ($log) =>
            $log->tipe === 'berkurang'
            && ! $log->nomor_transaksi
            && str_starts_with((string) $log->keterangan, 'Pengisian bensin Kendaraan ')
        );

        if ($legacyHistories->isNotEmpty()) {
            $from = $legacyHistories->min('created_at')->copy()->subMinute();
            $to = $legacyHistories->max('created_at')->copy()->addMinute();
            $fuelEntries = BiayaBensin::with('mobil')
                ->whereBetween('created_at', [$from, $to])
                ->where('nomor_kartu', $card->nomor_kartu)
                ->whereIn('biaya', $legacyHistories->pluck('nominal')->unique())
                ->get(['id', 'created_at', 'created_by', 'biaya', 'mobil_id']);

            $legacyHistories->each(function ($log) use ($fuelEntries) {
                $plat = substr($log->keterangan, strlen('Pengisian bensin Kendaraan '));
                $matches = $fuelEntries->filter(fn ($fuel) =>
                    $fuel->created_by == $log->created_by
                    && abs($fuel->created_at->diffInSeconds($log->created_at)) <= 60
                    && abs((float) $fuel->biaya - (float) $log->nominal) < 0.01
                    && ($plat === '-' || $fuel->mobil?->nomor_polisi === $plat)
                );

                if ($matches->count() === 1) {
                    $log->setAttribute('legacy_nomor_transaksi', $matches->first()->nomor_transaksi);
                }
            });
        }

        return view('master-kartu-bensin-batam.history', compact('card', 'histories'));
    }

    public function editHistory($id, $historyId)
    {
        $card = MasterKartuBensinBatam::findOrFail($id);
        $log = $card->histories()->findOrFail($historyId);

        return view('master-kartu-bensin-batam.edit-history', compact('card', 'log'));
    }

    public function updateHistory(Request $request, $id, $historyId)
    {
        $existingLog = MasterKartuBensinBatam::findOrFail($id)->histories()->findOrFail($historyId);
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|gt:0',
            'keterangan' => 'nullable|string|max:1000',
            'biaya_admin' => $existingLog->nomor_pranota ? 'required|numeric|min:0|max:9999999999999.99' : 'prohibited',
        ]);

        DB::transaction(function () use ($id, $historyId, $validated) {
            $card = MasterKartuBensinBatam::lockForUpdate()->findOrFail($id);
            $log = $card->histories()->lockForUpdate()->findOrFail($historyId);
            $oldNominal = (int) round((float) $log->nominal * 100);
            $newNominal = (int) round((float) $validated['nominal'] * 100);
            $delta = ($newNominal - $oldNominal) * ($log->tipe === 'berkurang' ? -1 : 1);
            $newCardSaldo = (int) round((float) $card->saldo * 100) + $delta;
            if ($newCardSaldo < 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'nominal' => 'Perubahan nominal membuat saldo kartu menjadi negatif.',
                ]);
            }

            $following = $card->histories()->where('id', '>=', $log->id)->orderBy('id')->lockForUpdate()->get();
            foreach ($following as $entry) {
                $saldoSesudah = (int) round((float) $entry->saldo_sesudah * 100) + $delta;
                if ($saldoSesudah < 0) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'nominal' => 'Perubahan nominal membuat saldo riwayat menjadi negatif.',
                    ]);
                }
                $entry->saldo_sesudah = $saldoSesudah / 100;
                if ($entry->id !== $log->id) {
                    $saldoSebelum = (int) round((float) $entry->saldo_sebelum * 100) + $delta;
                    if ($saldoSebelum < 0) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'nominal' => 'Perubahan nominal membuat saldo riwayat menjadi negatif.',
                        ]);
                    }
                    $entry->saldo_sebelum = $saldoSebelum / 100;
                }
                $entry->save();
            }

            $log->update($validated);
            $card->update([
                'saldo' => $newCardSaldo / 100,
                'updated_by' => auth()->id(),
            ]);
        });

        return redirect()->route('master-kartu-bensin-batam.history', $id)
            ->with('success', 'Riwayat kartu bensin berhasil diperbarui.');
    }

    public function topup(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date_format:Y-m-d',
            'nominal' => 'required|integer|min:1',
            'biaya_admin' => 'required|numeric|min:0|max:9999999999999.99',
            'keterangan' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($validated, $id) {
            $card = MasterKartuBensinBatam::lockForUpdate()->findOrFail($id);
            $saldoSebelum = $card->saldo;
            $saldoSesudah = round((float) $saldoSebelum + (float) $validated['nominal'], 2);

            $card->update(['saldo' => $saldoSesudah, 'updated_by' => auth()->id()]);
            $log = $card->histories()->create([
                'tanggal' => $validated['tanggal'],
                'tipe' => 'bertambah',
                'nominal' => $validated['nominal'],
                'biaya_admin' => $validated['biaya_admin'],
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $saldoSesudah,
                'keterangan' => $validated['keterangan'],
                'created_by' => auth()->id(),
            ]);
            $log->update([
                'nomor_pranota' => $this->makePranotaNumber($log),
            ]);
        });

        return redirect()->route('master-kartu-bensin-batam.history', $id)
            ->with('success', 'Top up saldo kartu bensin berhasil disimpan.');
    }

    public function convertHistoryToPranota(Request $request, $id, $historyId)
    {
        $validated = $request->validate([
            'biaya_admin' => 'required|numeric|min:0|max:9999999999999.99',
        ]);

        DB::transaction(function () use ($id, $historyId, $validated) {
            $card = MasterKartuBensinBatam::findOrFail($id);
            $log = $card->histories()->lockForUpdate()->findOrFail($historyId);

            if ($log->tipe !== 'bertambah' || $log->nomor_pranota) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'biaya_admin' => 'Riwayat ini tidak dapat dijadikan pranota.',
                ]);
            }

            $log->update([
                'nomor_pranota' => $this->makePranotaNumber($log),
                'biaya_admin' => $validated['biaya_admin'],
            ]);
        });

        return redirect()->route('master-kartu-bensin-batam.history', $id)
            ->with('success', 'Riwayat top up berhasil dijadikan pranota.');
    }

    private function makePranotaNumber(\App\Models\MasterKartuBensinBatamHistory $log): string
    {
        return 'PR-KBB-'.$log->tanggal->format('Ym').'-'.str_pad((string) $log->id, 6, '0', STR_PAD_LEFT);
    }

    public function showPranota($id, $historyId)
    {
        $card = MasterKartuBensinBatam::findOrFail($id);
        $log = $card->histories()->whereNotNull('nomor_pranota')->findOrFail($historyId);

        return view('master-kartu-bensin-batam.pranota', compact('card', 'log'));
    }
}
