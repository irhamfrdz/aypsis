<?php

namespace App\Http\Controllers;

use App\Models\JurnalOppOpt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class JurnalOppOptController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'tanggal_dari' => ['nullable', 'date'],
            'tanggal_sampai' => ['nullable', 'date', 'after_or_equal:tanggal_dari'],
        ]);

        $query = JurnalOppOpt::query()->with('creator');

        $query->when($filters['search'] ?? null, function ($query, string $search) {
            $query->where(function ($query) use ($search) {
                $query->where('nomor_referensi', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        });
        $query->when($filters['tanggal_dari'] ?? null, fn ($query, $date) => $query->whereDate('tanggal', '>=', $date));
        $query->when($filters['tanggal_sampai'] ?? null, fn ($query, $date) => $query->whereDate('tanggal', '<=', $date));

        $summaryQuery = clone $query;
        $totalSaldo = (float) (clone $summaryQuery)->sum('saldo');
        $totalKredit = (float) (clone $summaryQuery)->sum('kredit');
        $saldoAkhir = (float) (JurnalOppOpt::query()
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->value('saldo_berjalan') ?? 0);

        $entries = $query
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('jurnal-opp-opt.index', compact('entries', 'totalSaldo', 'totalKredit', 'saldoAkhir'));
    }

    public function create(): View
    {
        return view('jurnal-opp-opt.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data) {
            JurnalOppOpt::create($data + [
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
            $this->recalculateBalances();
        });

        return redirect()->route('jurnal-opp-opt.index')
            ->with('success', 'Transaksi Jurnal OPT/OPP berhasil ditambahkan.');
    }

    public function edit(JurnalOppOpt $jurnalOppOpt): View
    {
        return view('jurnal-opp-opt.edit', compact('jurnalOppOpt'));
    }

    public function update(Request $request, JurnalOppOpt $jurnalOppOpt): RedirectResponse
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data, $jurnalOppOpt) {
            $jurnalOppOpt->update($data + ['updated_by' => auth()->id()]);
            $this->recalculateBalances();
        });

        return redirect()->route('jurnal-opp-opt.index')
            ->with('success', 'Transaksi Jurnal OPT/OPP berhasil diperbarui.');
    }

    public function destroy(JurnalOppOpt $jurnalOppOpt): RedirectResponse
    {
        DB::transaction(function () use ($jurnalOppOpt) {
            $jurnalOppOpt->delete();
            $this->recalculateBalances();
        });

        return redirect()->route('jurnal-opp-opt.index')
            ->with('success', 'Transaksi Jurnal OPT/OPP berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'nomor_referensi' => ['nullable', 'string', 'max:100'],
            'keterangan' => ['required', 'string', 'max:2000'],
            'saldo' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'kredit' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
        ]);

        $data['saldo'] = (float) ($data['saldo'] ?? 0);
        $data['kredit'] = (float) ($data['kredit'] ?? 0);

        if ($data['saldo'] <= 0 && $data['kredit'] <= 0) {
            throw ValidationException::withMessages([
                'saldo' => 'Isi saldo atau kredit dengan nilai lebih dari nol.',
                'kredit' => 'Isi saldo atau kredit dengan nilai lebih dari nol.',
            ]);
        }

        return $data;
    }

    private function recalculateBalances(): void
    {
        $runningBalanceInCents = 0;
        $entries = JurnalOppOpt::query()
            ->orderBy('tanggal')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($entries as $entry) {
            $runningBalanceInCents += (int) round((float) $entry->saldo * 100)
                - (int) round((float) $entry->kredit * 100);
            $entry->forceFill(['saldo_berjalan' => $runningBalanceInCents / 100])->saveQuietly();
        }
    }
}
