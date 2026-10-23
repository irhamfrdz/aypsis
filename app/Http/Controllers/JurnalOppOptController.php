<?php

namespace App\Http\Controllers;

use App\Models\JurnalOppOpt;
use App\Models\KlasifikasiBiaya;
use App\Models\Manifest;
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

        $query = JurnalOppOpt::query()->with(['creator', 'klasifikasiBiaya', 'manifestItems']);

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

    public function create(Request $request): View
    {
        $mode = $request->query('mode') === 'voyage' ? 'voyage' : 'manual';
        $kapals = $mode === 'voyage'
            ? Manifest::query()->whereNotNull('nama_kapal')->where('nama_kapal', '!=', '')
                ->distinct()->orderBy('nama_kapal')->pluck('nama_kapal')
            : collect();
        $klasifikasiBiayas = KlasifikasiBiaya::query()->where('is_active', true)->orderBy('nama')->get();

        return view('jurnal-opp-opt.create', compact('mode', 'kapals', 'klasifikasiBiayas'));
    }

    public function manifestOptions(Request $request)
    {
        $request->validate([
            'nama_kapal' => ['nullable', 'string', 'max:255'],
            'no_voyage' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Manifest::query();
        if ($request->filled('nama_kapal')) {
            $query->where('nama_kapal', $request->input('nama_kapal'));
        }
        if ($request->filled('no_voyage')) {
            $query->where('no_voyage', $request->input('no_voyage'));
        }
        if (! $request->filled('nama_kapal')) {
            return response()->json(['voyages' => [], 'references' => []]);
        }

        if (! $request->filled('no_voyage')) {
            return response()->json([
                'voyages' => (clone $query)->whereNotNull('no_voyage')->where('no_voyage', '!=', '')
                    ->distinct()->orderBy('no_voyage')->pluck('no_voyage')->values(),
                'references' => [],
            ]);
        }

        return response()->json([
            'voyages' => [],
            'references' => (clone $query)
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->whereNotNull('nomor_kontainer')->where('nomor_kontainer', '!=', '');
                    })->orWhere(function ($query) {
                        $query->whereNotNull('nomor_bl')->where('nomor_bl', '!=', '');
                    });
                })
                ->select('nomor_kontainer', 'nomor_bl')->distinct()
                ->orderBy('nomor_kontainer')->orderBy('nomor_bl')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $manifestItems = $data['manifest_items'] ?? [];
        unset($data['manifest_items']);

        DB::transaction(function () use ($data, $manifestItems) {
            $entry = JurnalOppOpt::create($data + [
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
            $this->syncManifestItems($entry, $manifestItems);
            $this->recalculateBalances();
        });

        return redirect()->route('jurnal-opp-opt.index')
            ->with('success', 'Transaksi Jurnal OPT/OPP berhasil ditambahkan.');
    }

    public function edit(JurnalOppOpt $jurnalOppOpt): View
    {
        $mode = $jurnalOppOpt->nama_kapal ? 'voyage' : 'manual';
        $kapals = $mode === 'voyage'
            ? Manifest::query()->whereNotNull('nama_kapal')->where('nama_kapal', '!=', '')
                ->distinct()->orderBy('nama_kapal')->pluck('nama_kapal')
            : collect();
        $klasifikasiBiayas = KlasifikasiBiaya::query()
            ->where(function ($query) use ($jurnalOppOpt) {
                $query->where('is_active', true);
                if ($jurnalOppOpt->klasifikasi_biaya_id) {
                    $query->orWhereKey($jurnalOppOpt->klasifikasi_biaya_id);
                }
            })
            ->orderBy('nama')->get();

        $selectedManifestReferences = $jurnalOppOpt->manifestItems->map(fn ($item) => json_encode([
            'nomor_kontainer' => $item->nomor_kontainer,
            'nomor_bl' => $item->nomor_bl,
        ]))->values();
        if ($selectedManifestReferences->isEmpty() && ($jurnalOppOpt->nomor_kontainer || $jurnalOppOpt->nomor_bl)) {
            $selectedManifestReferences->push(json_encode([
                'nomor_kontainer' => $jurnalOppOpt->nomor_kontainer,
                'nomor_bl' => $jurnalOppOpt->nomor_bl,
            ]));
        }

        return view('jurnal-opp-opt.edit', compact('jurnalOppOpt', 'mode', 'kapals', 'klasifikasiBiayas', 'selectedManifestReferences'));
    }

    public function update(Request $request, JurnalOppOpt $jurnalOppOpt): RedirectResponse
    {
        $data = $this->validatedData($request);
        $manifestItems = $data['manifest_items'] ?? [];
        unset($data['manifest_items']);

        DB::transaction(function () use ($data, $manifestItems, $jurnalOppOpt) {
            $jurnalOppOpt->update($data + ['updated_by' => auth()->id()]);
            $this->syncManifestItems($jurnalOppOpt, $manifestItems);
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
            'mode' => ['nullable', 'in:manual,voyage'],
            'tanggal' => ['required', 'date'],
            'nomor_referensi' => ['nullable', 'string', 'max:100'],
            'nama_kapal' => ['nullable', 'required_if:mode,voyage', 'string', 'max:255'],
            'no_voyage' => ['nullable', 'required_if:mode,voyage', 'string', 'max:255'],
            'manifest_references' => ['nullable', 'array', 'max:100'],
            'manifest_references.*' => ['required', 'string', 'max:600'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'klasifikasi_biaya_id' => ['nullable', 'integer', 'exists:klasifikasi_biayas,id'],
            'keterangan' => ['required', 'string', 'max:2000'],
            'saldo' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'kredit' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
        ]);

        $data['saldo'] = (float) ($data['saldo'] ?? 0);
        $data['kredit'] = (float) ($data['kredit'] ?? 0);

        if (($data['mode'] ?? null) === 'voyage') {
            $manifestQuery = Manifest::query()
                ->where('nama_kapal', $data['nama_kapal'])
                ->where('no_voyage', $data['no_voyage']);

            if (! $manifestQuery->exists()) {
                throw ValidationException::withMessages(['no_voyage' => 'Voyage tidak ditemukan pada manifest untuk kapal yang dipilih.']);
            }
            $references = [];
            foreach ($data['manifest_references'] ?? [] as $encodedReference) {
                try {
                    $reference = json_decode($encodedReference, true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException) {
                    throw ValidationException::withMessages(['manifest_references' => 'Referensi kontainer / BL tidak valid.']);
                }

                if (! is_array($reference)
                    || (! array_key_exists('nomor_kontainer', $reference) && ! array_key_exists('nomor_bl', $reference))
                    || (isset($reference['nomor_kontainer']) && ! is_string($reference['nomor_kontainer']))
                    || (isset($reference['nomor_bl']) && ! is_string($reference['nomor_bl']))) {
                    throw ValidationException::withMessages(['manifest_references' => 'Referensi kontainer / BL tidak valid.']);
                }

                $container = $reference['nomor_kontainer'] ?? null;
                $bl = $reference['nomor_bl'] ?? null;
                if (($container === null || $container === '') && ($bl === null || $bl === '')) {
                    throw ValidationException::withMessages(['manifest_references' => 'Setiap pilihan harus memiliki nomor kontainer atau BL.']);
                }

                $referenceQuery = clone $manifestQuery;
                $this->whereManifestField($referenceQuery, 'nomor_kontainer', $container);
                $this->whereManifestField($referenceQuery, 'nomor_bl', $bl);
                if (! $referenceQuery->exists()) {
                    throw ValidationException::withMessages(['manifest_references' => 'Pasangan kontainer dan BL tidak ditemukan pada baris manifest yang sama.']);
                }

                $key = json_encode(['nomor_kontainer' => $container, 'nomor_bl' => $bl]);
                $references[$key] = ['nomor_kontainer' => $container, 'nomor_bl' => $bl];
            }

            $references = array_values($references);
            $data['nomor_kontainer'] = count($references) === 1 ? $references[0]['nomor_kontainer'] : null;
            $data['nomor_bl'] = count($references) === 1 ? $references[0]['nomor_bl'] : null;
        } else {
            $data['nama_kapal'] = null;
            $data['no_voyage'] = null;
            $data['nomor_kontainer'] = null;
            $data['nomor_bl'] = null;
            $references = [];
        }
        if (! empty($data['klasifikasi_biaya_id']) && ! KlasifikasiBiaya::query()
            ->whereKey($data['klasifikasi_biaya_id'])->where('is_active', true)->exists()) {
            throw ValidationException::withMessages(['klasifikasi_biaya_id' => 'Klasifikasi biaya yang dipilih tidak aktif.']);
        }
        $data['manifest_items'] = $this->allocateAmountsToReferences($references, $data['saldo'], $data['kredit']);
        unset($data['manifest_references']);
        unset($data['mode']);

        if ($data['saldo'] <= 0 && $data['kredit'] <= 0) {
            throw ValidationException::withMessages([
                'saldo' => 'Isi saldo atau kredit dengan nilai lebih dari nol.',
                'kredit' => 'Isi saldo atau kredit dengan nilai lebih dari nol.',
            ]);
        }

        return $data;
    }

    private function whereManifestField($query, string $field, ?string $value): void
    {
        if ($value === null || $value === '') {
            $query->where(function ($query) use ($field) {
                $query->whereNull($field)->orWhere($field, '');
            });

            return;
        }

        $query->where($field, $value);
    }

    private function allocateAmountsToReferences(array $references, float $saldo, float $kredit): array
    {
        if ($references === []) {
            return [];
        }

        $groups = [];
        foreach ($references as $index => $reference) {
            $container = $reference['nomor_kontainer'] ?? null;
            $bl = $reference['nomor_bl'] ?? null;
            $groupKey = $container !== null && $container !== ''
                ? 'container:'.$container
                : 'bl:'.($bl ?? '');
            $groups[$groupKey][] = $index;
        }

        $count = count($groups);
        $saldoCents = (int) round($saldo * 100);
        $kreditCents = (int) round($kredit * 100);
        $saldoBase = intdiv($saldoCents, $count);
        $kreditBase = intdiv($kreditCents, $count);
        $saldoRemainder = $saldoCents % $count;
        $kreditRemainder = $kreditCents % $count;

        foreach ($references as &$reference) {
            $reference['saldo'] = 0;
            $reference['kredit'] = 0;
        }
        unset($reference);

        $groupIndex = 0;
        foreach ($groups as $indexes) {
            $firstIndex = $indexes[0];
            $references[$firstIndex]['saldo'] = ($saldoBase + ($groupIndex < $saldoRemainder ? 1 : 0)) / 100;
            $references[$firstIndex]['kredit'] = ($kreditBase + ($groupIndex < $kreditRemainder ? 1 : 0)) / 100;
            $groupIndex++;
        }

        return $references;
    }

    private function syncManifestItems(JurnalOppOpt $entry, array $items): void
    {
        $entry->manifestItems()->delete();
        if ($items !== []) {
            $entry->manifestItems()->createMany($items);
        }
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
