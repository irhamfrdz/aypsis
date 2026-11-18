<?php

namespace App\Http\Controllers;

use App\Models\SuratJalanBongkaranTanjungPinang;
use App\Models\Manifest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuratJalanBongkaranTanjungPinangController extends Controller
{
    public function selectShip(Request $request)
    {
        $kapals = Manifest::query()->whereNotNull('nama_kapal')->where('nama_kapal', '<>', '')
            ->select('nama_kapal')->distinct()->orderBy('nama_kapal')->pluck('nama_kapal');
        $voyages = collect();
        if ($request->filled('nama_kapal')) {
            $voyages = Manifest::query()->where('nama_kapal', $request->string('nama_kapal')->toString())
                ->whereNotNull('no_voyage')->where('no_voyage', '<>', '')
                ->select('no_voyage')->distinct()->orderByDesc('no_voyage')->pluck('no_voyage');
        }

        return view('surat-jalan-bongkaran-tanjung-pinang.select-ship', compact('kapals', 'voyages'));
    }

    public function getVoyages(Request $request)
    {
        $request->validate(['nama_kapal' => ['required', 'string', 'max:255']]);
        $voyages = Manifest::query()->where('nama_kapal', $request->string('nama_kapal')->toString())
            ->whereNotNull('no_voyage')->where('no_voyage', '<>', '')
            ->select('no_voyage')->distinct()->orderByDesc('no_voyage')->pluck('no_voyage');

        return response()->json(['success' => true, 'voyages' => $voyages]);
    }

    public function index(Request $request)
    {
        $request->validate([
            'nama_kapal' => ['required', 'string', 'max:255'],
            'no_voyage' => ['required', 'string', 'max:255'],
            'mode' => ['nullable', 'in:manifest,surat_jalan'],
        ]);
        $selectedKapal = $request->string('nama_kapal')->toString();
        $selectedVoyage = $request->string('no_voyage')->toString();
        $search = trim((string) $request->query('search', ''));
        $mode = $request->query('mode', 'manifest');
        $manifestQuery = Manifest::query()->where('nama_kapal', $selectedKapal)->where('no_voyage', $selectedVoyage)
            ->with('suratJalanBongkaranTanjungPinang')
            ->when($search !== '', fn ($query) => $query->where(function ($q) use ($search) {
                $q->where('nomor_bl', 'like', "%{$search}%")
                    ->orWhere('nomor_kontainer', 'like', "%{$search}%")
                    ->orWhere('no_seal', 'like', "%{$search}%")
                    ->orWhere('nama_barang', 'like', "%{$search}%")
                    ->orWhere('penerima', 'like', "%{$search}%");
            }));
        $stats = [
            'manifest' => (clone $manifestQuery)->count(),
            'sudah_sj' => (clone $manifestQuery)->whereHas('suratJalanBongkaranTanjungPinang')->count(),
            'belum_sj' => (clone $manifestQuery)->whereDoesntHave('suratJalanBongkaranTanjungPinang')->count(),
        ];

        $manifests = $mode === 'manifest'
            ? $manifestQuery->orderBy('nomor_urut')->paginate(25)->withQueryString()
            : collect();
        $suratJalans = $mode === 'surat_jalan'
            ? SuratJalanBongkaranTanjungPinang::query()->where('nama_kapal', $selectedKapal)->where('no_voyage', $selectedVoyage)
                ->when($search !== '', fn ($query) => $query->where(function ($q) use ($search) {
                    $q->where('nomor_surat_jalan', 'like', "%{$search}%")->orWhere('no_bl', 'like', "%{$search}%")
                        ->orWhere('no_kontainer', 'like', "%{$search}%");
                }))->orderByDesc('tanggal_surat_jalan')->orderByDesc('id')->paginate(25)->withQueryString()
            : collect();

        $nomorSuratJalanPreview = $this->formatNomorSuratJalan(
            (int) SuratJalanBongkaranTanjungPinang::max('id') + 1,
            now()->toDateString()
        );

        return view('surat-jalan-bongkaran-tanjung-pinang.index', compact('manifests', 'suratJalans', 'search', 'stats', 'selectedKapal', 'selectedVoyage', 'mode', 'nomorSuratJalanPreview'));
    }

    public function create(Request $request)
    {
        $manifest = $request->filled('manifest_id') ? Manifest::findOrFail($request->integer('manifest_id')) : null;
        $suratJalan = new SuratJalanBongkaranTanjungPinang([
            'manifest_id' => $manifest?->id,
            'nama_kapal' => $manifest?->nama_kapal ?? $request->query('nama_kapal'),
            'no_voyage' => $manifest?->no_voyage ?? $request->query('no_voyage'),
            'no_bl' => $manifest?->nomor_bl,
            'pengirim' => $manifest?->pengirim,
            'penerima' => $manifest?->penerima,
            'jenis_barang' => $manifest?->nama_barang,
            'no_kontainer' => $manifest?->nomor_kontainer,
            'no_seal' => $manifest?->no_seal,
            'size' => $manifest?->size_kontainer,
            'tipe_kontainer' => $manifest?->tipe_kontainer,
            'term' => $manifest?->term,
        ]);

        return view('surat-jalan-bongkaran-tanjung-pinang.form', [
            'suratJalan' => $suratJalan,
            'nomorSuratJalanPreview' => $this->formatNomorSuratJalan(
                $suratJalan->exists ? $suratJalan->id : (int) SuratJalanBongkaranTanjungPinang::max('id') + 1,
                old('tanggal_surat_jalan', now()->toDateString())
            ),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['input_by'] = Auth::id();
        $data['input_date'] = now();
        $suratJalan = DB::transaction(function () use ($data) {
            // Insert with a unique temporary value so the database can allocate the ID,
            // then derive the permanent, automatically assigned number from that ID.
            $data['nomor_surat_jalan'] = 'TEMP-'.Str::uuid();
            $record = SuratJalanBongkaranTanjungPinang::create($data);
            $record->nomor_surat_jalan = $this->formatNomorSuratJalan($record->id, $record->tanggal_surat_jalan->toDateString());
            $record->save();

            return $record;
        });

        return redirect()->route('surat-jalan-bongkaran-tanjung-pinang.list', $this->selection($data))
            ->with('success', 'Surat jalan bongkaran Tanjung Pinang '.$suratJalan->nomor_surat_jalan.' berhasil dibuat.');
    }

    public function edit(int $id)
    {
        return view('surat-jalan-bongkaran-tanjung-pinang.form', [
            'suratJalan' => $this->find($id),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $suratJalan = $this->find($id);
        $data = $this->validated($request, $suratJalan);
        $suratJalan->update($data);

        return redirect()->route('surat-jalan-bongkaran-tanjung-pinang.list', $this->selection($data))
            ->with('success', 'Surat jalan bongkaran Tanjung Pinang berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id)
    {
        $suratJalan = $this->find($id);
        $suratJalan->delete();

        return redirect()->route('surat-jalan-bongkaran-tanjung-pinang.list', $this->selection($request->all() + $suratJalan->toArray()))
            ->with('success', 'Surat jalan bongkaran Tanjung Pinang berhasil dihapus.');
    }

    public function print(int $id)
    {
        $suratJalanBongkaran = $this->find($id);

        return view('surat-jalan-bongkaran.print', [
            'suratJalanBongkaran' => $suratJalanBongkaran,
            'pageNumber' => 1,
            'totalPages' => 1,
        ]);
    }

    private function find(int $id): SuratJalanBongkaranTanjungPinang
    {
        return SuratJalanBongkaranTanjungPinang::findOrFail($id);
    }

    private function formatNomorSuratJalan(int $id, string $tanggal): string
    {
        return sprintf('SJB/TP/%s/%06d', str_replace('-', '', $tanggal), $id);
    }

    private function selection(array $data): array
    {
        return array_filter([
            'nama_kapal' => $data['nama_kapal'] ?? null,
            'no_voyage' => $data['no_voyage'] ?? null,
        ], fn ($value) => filled($value));
    }

    private function validated(Request $request, ?SuratJalanBongkaranTanjungPinang $suratJalan = null): array
    {
        return $request->validate([
            'tanggal_surat_jalan' => ['required', 'date'],
            'manifest_id' => ['nullable', 'integer', 'exists:manifests,id'],
            'nama_kapal' => ['required', 'string', 'max:255'],
            'no_voyage' => ['required', 'string', 'max:255'],
            'no_bl' => ['nullable', 'string', 'max:255'],
            'pengirim' => ['nullable', 'string', 'max:255'],
            'penerima' => ['nullable', 'string', 'max:255'],
            'jenis_barang' => ['nullable', 'string', 'max:255'],
            'tujuan_pengambilan' => ['nullable', 'string', 'max:255'],
            'tujuan_pengiriman' => ['nullable', 'string', 'max:255'],
            'no_kontainer' => ['nullable', 'string', 'max:255'],
            'no_seal' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'tipe_kontainer' => ['nullable', 'string', 'max:255'],
            'term' => ['nullable', 'string', 'max:255'],
            'aktifitas' => ['nullable', 'string'],
        ]);
    }
}
