<?php

namespace App\Http\Controllers;

use App\Models\TagihanServiceKendaraanBatam;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TagihanServiceKendaraanBatamController extends Controller
{
    public function index(Request $request)
    {
        $query = TagihanServiceKendaraanBatam::query();

        if (in_array($request->input('jenis_tagihan'), ['kendaraan', 'lain_lain'], true)) {
            $query->where('jenis_tagihan', $request->input('jenis_tagihan'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('nomor_tagihan', 'like', "%{$search}%")
                    ->orWhere('nomor_polisi', 'like', "%{$search}%")
                    ->orWhere('bengkel', 'like', "%{$search}%")
                    ->orWhere('rincian_service', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_service', '>=', $request->input('tanggal_dari'));
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_service', '<=', $request->input('tanggal_sampai'));
        }

        $tagihan = $query->latest('tanggal_service')->latest('id')->paginate(20)->withQueryString();

        return view('tagihan-lain-lain-batam.index', compact('tagihan'));
    }

    public function create()
    {
        return view('tagihan-lain-lain-batam.create', [
            'tagihan' => new TagihanServiceKendaraanBatam,
            'nomorTagihan' => $this->previewNomorTagihan(),
            'mobils' => $this->getMobilsForSelect(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data = $this->applyMobilNumber($data);
        DB::transaction(function () use (&$data) {
            $prefix = '001'.now()->format('dm');
            DB::table('tagihan_lain_lain_batam_sequences')->insertOrIgnore([
                'kode_tanggal_bulan' => $prefix,
                'nomor_terakhir' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $sequence = DB::table('tagihan_lain_lain_batam_sequences')
                ->where('kode_tanggal_bulan', $prefix)
                ->lockForUpdate()
                ->first();
            $nextNumber = $sequence->nomor_terakhir + 1;

            DB::table('tagihan_lain_lain_batam_sequences')
                ->where('kode_tanggal_bulan', $prefix)
                ->update(['nomor_terakhir' => $nextNumber, 'updated_at' => now()]);

            $data['nomor_tagihan'] = $prefix.str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
            $data['created_by'] = Auth::id();
            TagihanServiceKendaraanBatam::create($data);
        });

        return redirect()->route('tagihan-lain-lain-batam.index')
            ->with('success', 'Tagihan lain lain Batam berhasil ditambahkan.');
    }

    public function edit(TagihanServiceKendaraanBatam $serviceTagihan)
    {
        $mobils = $this->getMobilsForSelect();
        $selectedMobilId = $serviceTagihan->mobil_id ?: $mobils->first(function ($mobil) use ($serviceTagihan) {
            return $this->mobilDisplayNumber($mobil) === $serviceTagihan->nomor_polisi;
        })?->id;

        return view('tagihan-lain-lain-batam.edit', compact('serviceTagihan', 'mobils', 'selectedMobilId') + [
            'tagihan' => $serviceTagihan,
        ]);
    }

    public function update(Request $request, TagihanServiceKendaraanBatam $serviceTagihan)
    {
        $data = $this->validated($request);
        $data = $this->applyMobilNumber($data);
        $data['updated_by'] = Auth::id();
        $serviceTagihan->update($data);

        return redirect()->route('tagihan-lain-lain-batam.index')
            ->with('success', 'Tagihan lain lain Batam berhasil diperbarui.');
    }

    public function destroy(TagihanServiceKendaraanBatam $serviceTagihan)
    {
        $serviceTagihan->delete();

        return redirect()->route('tagihan-lain-lain-batam.index')
            ->with('success', 'Tagihan lain lain Batam berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'jenis_tagihan' => ['required', 'in:kendaraan,lain_lain'],
            'nomor_tagihan' => ['nullable', 'string', 'max:100'],
            'tanggal_service' => ['required', 'date'],
            'mobil_id' => ['nullable', 'required_if:jenis_tagihan,kendaraan', 'integer', 'exists:mobils,id'],
            'bengkel' => ['required', 'string', 'max:255'],
            'rincian_service' => ['required', 'string', 'max:2000'],
            'biaya' => ['required', 'numeric', 'min:0'],
            'pph_rate' => ['required', 'numeric', 'in:0,2,2.5'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function getMobilsForSelect()
    {
        return Mobil::query()
            ->select(['id', 'kode_no', 'nomor_polisi', 'no_kir', 'nickname'])
            ->where(function ($query) {
                $query->whereNotNull('nomor_polisi')->orWhereNotNull('no_kir');
            })
            ->orderBy('nomor_polisi')
            ->get()
            ->filter(fn (Mobil $mobil) => $this->mobilDisplayNumber($mobil) !== '')
            ->values();
    }

    private function mobilDisplayNumber(Mobil $mobil): string
    {
        $nomorPolisi = trim((string) $mobil->nomor_polisi);

        return $nomorPolisi !== '' ? $nomorPolisi : trim((string) $mobil->no_kir);
    }

    private function applyMobilNumber(array $data): array
    {
        if ($data['jenis_tagihan'] === 'kendaraan') {
            $mobil = Mobil::findOrFail($data['mobil_id']);
            $data['nomor_polisi'] = $this->mobilDisplayNumber($mobil);
        } else {
            $data['mobil_id'] = null;
            $data['nomor_polisi'] = null;
        }

        $data['total_biaya'] = round((float) $data['biaya'] * (1 - ((float) $data['pph_rate'] / 100)), 2);

        return $data;
    }

    private function previewNomorTagihan(): string
    {
        $prefix = '001'.now()->format('dm');
        $lastNumber = DB::table('tagihan_lain_lain_batam_sequences')
            ->where('kode_tanggal_bulan', $prefix)
            ->value('nomor_terakhir') ?? 0;

        return $prefix.str_pad((string) ($lastNumber + 1), 6, '0', STR_PAD_LEFT);
    }
}
