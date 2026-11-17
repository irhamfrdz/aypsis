<?php

namespace App\Http\Controllers;

use App\Models\SuratJalanBongkaranTanjungPinang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SuratJalanBongkaranTanjungPinangController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $query = SuratJalanBongkaranTanjungPinang::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nomor_surat_jalan', 'like', "%{$search}%")
                        ->orWhere('no_bl', 'like', "%{$search}%")
                        ->orWhere('no_kontainer', 'like', "%{$search}%")
                        ->orWhere('supir', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('nama_kapal'), fn ($query) => $query->where('nama_kapal', $request->string('nama_kapal')->toString()))
            ->when($request->filled('no_voyage'), fn ($query) => $query->where('no_voyage', $request->string('no_voyage')->toString()));

        $stats = [
            'total' => (clone $query)->count(),
            'kontainer' => (clone $query)->whereNotNull('no_kontainer')->where('no_kontainer', '<>', '')->count(),
            'voyage' => (clone $query)->whereNotNull('no_voyage')->distinct()->count('no_voyage'),
        ];
        $kapals = SuratJalanBongkaranTanjungPinang::query()->whereNotNull('nama_kapal')->where('nama_kapal', '<>', '')
            ->distinct()->orderBy('nama_kapal')->pluck('nama_kapal');
        $voyages = SuratJalanBongkaranTanjungPinang::query()->when($request->filled('nama_kapal'), fn ($query) => $query->where('nama_kapal', $request->string('nama_kapal')->toString()))
            ->whereNotNull('no_voyage')->where('no_voyage', '<>', '')->distinct()->orderByDesc('no_voyage')->pluck('no_voyage');
        $suratJalans = $query->orderByDesc('tanggal_surat_jalan')->orderByDesc('id')->paginate(25)
            ->withQueryString();

        return view('surat-jalan-bongkaran-tanjung-pinang.index', compact('suratJalans', 'search', 'stats', 'kapals', 'voyages'));
    }

    public function create()
    {
        return view('surat-jalan-bongkaran-tanjung-pinang.form', [
            'suratJalan' => new SuratJalanBongkaranTanjungPinang,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['input_by'] = Auth::id();
        $data['input_date'] = now();
        SuratJalanBongkaranTanjungPinang::create($data);

        return redirect()->route('surat-jalan-bongkaran-tanjung-pinang.index')
            ->with('success', 'Surat jalan bongkaran Tanjung Pinang berhasil dibuat.');
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

        return redirect()->route('surat-jalan-bongkaran-tanjung-pinang.index')
            ->with('success', 'Surat jalan bongkaran Tanjung Pinang berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $this->find($id)->delete();

        return redirect()->route('surat-jalan-bongkaran-tanjung-pinang.index')
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

    private function validated(Request $request, ?SuratJalanBongkaranTanjungPinang $suratJalan = null): array
    {
        return $request->validate([
            'nomor_surat_jalan' => ['required', 'string', 'max:255', Rule::unique('surat_jalan_bongkaran_tanjung_pinangs')->ignore($suratJalan?->id)],
            'tanggal_surat_jalan' => ['required', 'date'],
            'nama_kapal' => ['nullable', 'string', 'max:255'],
            'no_voyage' => ['nullable', 'string', 'max:255'],
            'no_bl' => ['nullable', 'string', 'max:255'],
            'pengirim' => ['nullable', 'string', 'max:255'],
            'penerima' => ['nullable', 'string', 'max:255'],
            'jenis_barang' => ['nullable', 'string', 'max:255'],
            'tujuan_pengambilan' => ['nullable', 'string', 'max:255'],
            'tujuan_pengiriman' => ['nullable', 'string', 'max:255'],
            'supir' => ['nullable', 'string', 'max:255'],
            'no_plat' => ['nullable', 'string', 'max:255'],
            'kenek' => ['nullable', 'string', 'max:255'],
            'no_kontainer' => ['nullable', 'string', 'max:255'],
            'no_seal' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'tipe_kontainer' => ['nullable', 'string', 'max:255'],
            'term' => ['nullable', 'string', 'max:255'],
            'aktifitas' => ['nullable', 'string'],
        ]);
    }
}
