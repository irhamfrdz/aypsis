<?php

namespace App\Http\Controllers;

use App\Models\SuratJalanBongkaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SuratJalanBongkaranTanjungPinangController extends Controller
{
    private const LOKASI = 'tanjung_pinang';

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $suratJalans = SuratJalanBongkaran::query()
            ->where('lokasi', self::LOKASI)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nomor_surat_jalan', 'like', "%{$search}%")
                        ->orWhere('no_bl', 'like', "%{$search}%")
                        ->orWhere('no_kontainer', 'like', "%{$search}%")
                        ->orWhere('supir', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('tanggal_surat_jalan')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('surat-jalan-bongkaran-tanjung-pinang.index', compact('suratJalans', 'search'));
    }

    public function create()
    {
        return view('surat-jalan-bongkaran-tanjung-pinang.form', [
            'suratJalan' => new SuratJalanBongkaran,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['lokasi'] = self::LOKASI;
        $data['input_by'] = Auth::id();
        $data['input_date'] = now();
        SuratJalanBongkaran::create($data);

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

    private function find(int $id): SuratJalanBongkaran
    {
        return SuratJalanBongkaran::where('lokasi', self::LOKASI)->findOrFail($id);
    }

    private function validated(Request $request, ?SuratJalanBongkaran $suratJalan = null): array
    {
        return $request->validate([
            'nomor_surat_jalan' => ['required', 'string', 'max:255', Rule::unique('surat_jalan_bongkarans')->ignore($suratJalan?->id)],
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
