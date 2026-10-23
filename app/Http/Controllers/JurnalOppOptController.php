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
        ]);

        $query = Manifest::query();
        if ($request->filled('nama_kapal')) {
            $query->where('nama_kapal', $request->input('nama_kapal'));
        }
        if ($request->filled('no_voyage')) {
            $query->where('no_voyage', $request->input('no_voyage'));
        }
        if (! $request->filled('nama_kapal')) {
            return response()->json(['voyages' => [], 'containers' => [], 'bls' => [], 'vendors' => []]);
        }

        if (! $request->filled('no_voyage')) {
            return response()->json([
                'voyages' => (clone $query)->whereNotNull('no_voyage')->where('no_voyage', '!=', '')
                    ->distinct()->orderBy('no_voyage')->pluck('no_voyage')->values(),
                'containers' => [],
                'bls' => [],
                'vendors' => [],
            ]);
        }

        // LCL dapat memiliki banyak baris manifest/vendor untuk satu kontainer.
        // DISTINCT membuat pilihan kontainer tetap satu per nomor kontainer.
        $vendorQuery = clone $query;
        if ($request->filled('nomor_kontainer')) {
            $vendorQuery->where('nomor_kontainer', $request->input('nomor_kontainer'));
        }
        if ($request->filled('nomor_bl')) {
            $vendorQuery->where('nomor_bl', $request->input('nomor_bl'));
        }

        return response()->json([
            'voyages' => [],
            'containers' => (clone $query)->whereNotNull('nomor_kontainer')->where('nomor_kontainer', '!=', '')
                ->distinct()->orderBy('nomor_kontainer')->pluck('nomor_kontainer')->values(),
            'bls' => (clone $query)->whereNotNull('nomor_bl')->where('nomor_bl', '!=', '')
                ->distinct()->orderBy('nomor_bl')->pluck('nomor_bl')->values(),
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
            foreach (['nomor_kontainer' => 'nomor_kontainer', 'nomor_bl' => 'nomor_bl'] as $field => $column) {
                if (! empty($data[$field]) && ! (clone $manifestQuery)->where($column, $data[$field])->exists()) {
                    throw ValidationException::withMessages([$field => 'Pilihan tidak ditemukan pada manifest voyage ini.']);
                }
            }
            $vendorManifestQuery = clone $manifestQuery;
            if (! empty($data['nomor_kontainer'])) {
                $vendorManifestQuery->where('nomor_kontainer', $data['nomor_kontainer']);
            }
            if (! empty($data['nomor_bl'])) {
                $vendorManifestQuery->where('nomor_bl', $data['nomor_bl']);
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
