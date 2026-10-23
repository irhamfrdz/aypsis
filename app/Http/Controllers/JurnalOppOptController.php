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

        $query = JurnalOppOpt::query()->with(['creator', 'klasifikasiBiaya']);

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
            'nomor_kontainer' => ['nullable', 'string', 'max:255'],
            'nomor_bl' => ['nullable', 'string', 'max:255'],
            'filter_reference' => ['nullable', 'boolean'],
        ]);

        $query = Manifest::query();
        if ($request->filled('nama_kapal')) {
            $query->where('nama_kapal', $request->input('nama_kapal'));
        }
        if ($request->filled('no_voyage')) {
            $query->where('no_voyage', $request->input('no_voyage'));
        }
        if (! $request->filled('nama_kapal')) {
            return response()->json(['voyages' => [], 'references' => [], 'vendors' => []]);
        }

        if (! $request->filled('no_voyage')) {
            return response()->json([
                'voyages' => (clone $query)->whereNotNull('no_voyage')->where('no_voyage', '!=', '')
                    ->distinct()->orderBy('no_voyage')->pluck('no_voyage')->values(),
                'references' => [],
                'vendors' => [],
            ]);
        }

        // Pasangan kontainer/BL berasal dari satu baris manifest; vendor LCL
        // tetap dipilih terpisah karena beberapa pengirim dapat berbagi kontainer.
        $vendorQuery = clone $query;
        if ($request->boolean('filter_reference')) {
            foreach (['nomor_kontainer', 'nomor_bl'] as $field) {
                $this->whereManifestField($vendorQuery, $field, $request->input($field));
            }
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
            'vendors' => $vendorQuery->whereNotNull('pengirim')->where('pengirim', '!=', '')
                ->distinct()->orderBy('pengirim')->pluck('pengirim')->values(),
        ]);
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

        return view('jurnal-opp-opt.edit', compact('jurnalOppOpt', 'mode', 'kapals', 'klasifikasiBiayas'));
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
            'mode' => ['nullable', 'in:manual,voyage'],
            'tanggal' => ['required', 'date'],
            'nomor_referensi' => ['nullable', 'string', 'max:100'],
            'nama_kapal' => ['nullable', 'required_if:mode,voyage', 'string', 'max:255'],
            'no_voyage' => ['nullable', 'required_if:mode,voyage', 'string', 'max:255'],
            'nomor_kontainer' => ['nullable', 'string', 'max:255'],
            'nomor_bl' => ['nullable', 'string', 'max:255'],
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
            if (($data['nomor_kontainer'] ?? null) !== null || ($data['nomor_bl'] ?? null) !== null) {
                $referenceQuery = clone $manifestQuery;
                foreach (['nomor_kontainer', 'nomor_bl'] as $field) {
                    $this->whereManifestField($referenceQuery, $field, $data[$field] ?? null);
                }
                if (! $referenceQuery->exists()) {
                    throw ValidationException::withMessages(['nomor_kontainer' => 'Pasangan kontainer dan BL tidak ditemukan pada baris manifest yang sama.']);
                }
            }
            $vendorManifestQuery = clone $manifestQuery;
            if (($data['nomor_kontainer'] ?? null) !== null || ($data['nomor_bl'] ?? null) !== null) {
                foreach (['nomor_kontainer', 'nomor_bl'] as $field) {
                    $this->whereManifestField($vendorManifestQuery, $field, $data[$field] ?? null);
                }
            }
            if (! empty($data['vendor']) && ! $vendorManifestQuery->where('pengirim', $data['vendor'])->exists()) {
                throw ValidationException::withMessages(['vendor' => 'Vendor/pengirim tidak ditemukan pada manifest voyage yang dipilih.']);
            }
        } else {
            $data['nama_kapal'] = null;
            $data['no_voyage'] = null;
            $data['nomor_kontainer'] = null;
            $data['nomor_bl'] = null;
        }
        if (! empty($data['klasifikasi_biaya_id']) && ! KlasifikasiBiaya::query()
            ->whereKey($data['klasifikasi_biaya_id'])->where('is_active', true)->exists()) {
            throw ValidationException::withMessages(['klasifikasi_biaya_id' => 'Klasifikasi biaya yang dipilih tidak aktif.']);
        }
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
