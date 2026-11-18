<?php

namespace App\Http\Controllers;

use App\Models\SuratJalanBongkaranTanjungPinang;
use App\Models\Manifest;
use App\Models\Gudang;
use App\Models\HistoryKontainer;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
            $this->recordCustomerWarehouseMovement($record);

            return $record;
        });

        return redirect()->route('surat-jalan-bongkaran-tanjung-pinang.list', $this->selection($data))
            ->with('success', 'Surat jalan bongkaran Tanjung Pinang '.$suratJalan->nomor_surat_jalan.' berhasil dibuat.');
    }

    public function storeBulk(Request $request)
    {
        $data = $request->validate([
            'nama_kapal' => ['required', 'string', 'max:255'],
            'no_voyage' => ['required', 'string', 'max:255'],
            'tanggal_surat_jalan' => ['required', 'date'],
            'uang_jalan' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'manifest_numbers' => ['required', 'array', 'min:1'],
            'manifest_numbers.*' => ['required', 'string', 'max:255', 'distinct'],
        ]);

        $jumlahDibuat = DB::transaction(function () use ($data) {
            $jumlahDibuat = 0;

            foreach ($data['manifest_numbers'] as $manifestNumber) {
                $manifest = Manifest::query()
                    ->where('nama_kapal', $data['nama_kapal'])
                    ->where('no_voyage', $data['no_voyage'])
                    ->where(function ($query) use ($manifestNumber) {
                        $query->where('nomor_bl', $manifestNumber)
                            ->orWhere('nomor_kontainer', $manifestNumber);
                    })
                    ->lockForUpdate()
                    ->get();

                if ($manifest->isEmpty()) {
                    throw ValidationException::withMessages([
                        'manifest_numbers' => "Manifest dengan nomor {$manifestNumber} tidak ditemukan pada kapal dan voyage yang dipilih.",
                    ]);
                }

                if ($manifest->count() > 1) {
                    throw ValidationException::withMessages([
                        'manifest_numbers' => "Nomor {$manifestNumber} cocok dengan lebih dari satu manifest. Gunakan Nomor BL yang unik.",
                    ]);
                }

                $manifest = $manifest->first();

                if ($manifest->suratJalanBongkaranTanjungPinang()->exists()) {
                    throw ValidationException::withMessages([
                        'manifest_numbers' => 'Manifest '.$manifest->nomor_bl.' sudah memiliki surat jalan.',
                    ]);
                }

                $record = SuratJalanBongkaranTanjungPinang::create([
                    'tanggal_surat_jalan' => $data['tanggal_surat_jalan'],
                    'nomor_surat_jalan' => 'TEMP-'.Str::uuid(),
                    'manifest_id' => $manifest->id,
                    'nama_kapal' => $manifest->nama_kapal,
                    'no_voyage' => $manifest->no_voyage,
                    'no_bl' => $manifest->nomor_bl,
                    'pengirim' => $manifest->pengirim,
                    'penerima' => $manifest->penerima,
                    'jenis_barang' => $manifest->nama_barang,
                    'no_kontainer' => $manifest->nomor_kontainer,
                    'no_seal' => $manifest->no_seal,
                    'size' => $manifest->size_kontainer,
                    'tipe_kontainer' => $manifest->tipe_kontainer,
                    'term' => $manifest->term,
                    'uang_jalan' => $data['uang_jalan'] ?? 0,
                    'input_by' => Auth::id(),
                    'input_date' => now(),
                ]);

                $record->nomor_surat_jalan = $this->formatNomorSuratJalan(
                    $record->id,
                    $record->tanggal_surat_jalan->toDateString()
                );
                $record->save();
                $this->recordCustomerWarehouseMovement($record);
                $jumlahDibuat++;
            }

            return $jumlahDibuat;
        });

        return response()->json([
            'success' => true,
            'message' => "{$jumlahDibuat} surat jalan berhasil dibuat.",
            'redirect' => route('surat-jalan-bongkaran-tanjung-pinang.list', [
                'nama_kapal' => $data['nama_kapal'],
                'no_voyage' => $data['no_voyage'],
                'mode' => 'manifest',
            ]),
        ]);
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

    private function recordCustomerWarehouseMovement(SuratJalanBongkaranTanjungPinang $suratJalan): void
    {
        $nomorKontainer = trim((string) $suratJalan->no_kontainer);
        if ($nomorKontainer === '') {
            return;
        }

        $gudangTujuan = Gudang::query()
            ->whereRaw('LOWER(TRIM(nama_gudang)) = ?', ['gudang customer tanjung pinang'])
            ->first();

        if (! $gudangTujuan) {
            throw ValidationException::withMessages([
                'no_kontainer' => 'Gudang "Gudang Customer Tanjung Pinang" belum terdaftar di master gudang.',
            ]);
        }

        $tanggalKegiatan = $suratJalan->tanggal_surat_jalan->toDateString();
        $riwayatSebelumnya = HistoryKontainer::query()
            ->where('nomor_kontainer', $nomorKontainer)
            ->whereDate('tanggal_kegiatan', '<=', $tanggalKegiatan)
            ->orderByDesc('tanggal_kegiatan')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        $kontainerSewa = Kontainer::query()
            ->where('nomor_seri_gabungan', $nomorKontainer)
            ->orderByDesc('id')
            ->first();
        $kontainerStok = StockKontainer::query()
            ->where('nomor_seri_gabungan', $nomorKontainer)
            ->orderByDesc('id')
            ->first();

        if (! $kontainerStok && ! $kontainerSewa) {
            throw ValidationException::withMessages([
                'no_kontainer' => 'Nomor kontainer belum terdaftar pada master kontainer atau stock kontainer, sehingga lokasinya tidak dapat diperbarui.',
            ]);
        }

        $gudangAsalId = $riwayatSebelumnya
            ? $riwayatSebelumnya->gudang_id
            : ($kontainerStok?->gudangs_id ?? $kontainerSewa?->gudangs_id);
        $tipeKontainer = $kontainerStok?->tipe_kontainer
            ?? $kontainerSewa?->tipe_kontainer
            ?? $suratJalan->tipe_kontainer;

        HistoryKontainer::create([
            'nomor_kontainer' => $nomorKontainer,
            'tipe_kontainer' => $tipeKontainer,
            'jenis_kegiatan' => 'Masuk',
            'tanggal_kegiatan' => $tanggalKegiatan,
            'asal_gudang_id' => $gudangAsalId,
            'gudang_id' => $gudangTujuan->id,
            'keterangan' => 'Pindah ke Gudang Customer Tanjung Pinang (Surat Jalan: '.$suratJalan->nomor_surat_jalan.')',
            'created_by' => Auth::id(),
        ]);
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
        $data = $request->validate([
            'tanggal_surat_jalan' => ['required', 'date'],
            'uang_jalan' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
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

        $data['uang_jalan'] = array_key_exists('uang_jalan', $data)
            ? ($data['uang_jalan'] ?? 0)
            : ($suratJalan?->uang_jalan ?? 0);

        return $data;
    }
}
