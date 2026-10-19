<?php

namespace App\Http\Controllers;

use App\Models\TagihanServiceKendaraanBatam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TagihanServiceKendaraanBatamController extends Controller
{
    public function index(Request $request)
    {
        $query = TagihanServiceKendaraanBatam::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('nomor_tagihan', 'like', "%{$search}%")
                    ->orWhere('nomor_polisi', 'like', "%{$search}%")
                    ->orWhere('bengkel', 'like', "%{$search}%")
                    ->orWhere('rincian_service', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_service', '>=', $request->input('tanggal_dari'));
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_service', '<=', $request->input('tanggal_sampai'));
        }

        $tagihan = $query->latest('tanggal_service')->latest('id')->paginate(20)->withQueryString();

        return view('tagihan-service-kendaraan-batam.index', compact('tagihan'));
    }

    public function create()
    {
        return view('tagihan-service-kendaraan-batam.create', [
            'tagihan' => new TagihanServiceKendaraanBatam,
            'nomorTagihan' => $this->previewNomorTagihan(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        DB::transaction(function () use (&$data) {
            $prefix = '001'.now()->format('dm');
            DB::table('tagihan_service_kendaraan_batam_sequences')->insertOrIgnore([
                'kode_tanggal_bulan' => $prefix,
                'nomor_terakhir' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $sequence = DB::table('tagihan_service_kendaraan_batam_sequences')
                ->where('kode_tanggal_bulan', $prefix)
                ->lockForUpdate()
                ->first();
            $nextNumber = $sequence->nomor_terakhir + 1;

            DB::table('tagihan_service_kendaraan_batam_sequences')
                ->where('kode_tanggal_bulan', $prefix)
                ->update(['nomor_terakhir' => $nextNumber, 'updated_at' => now()]);

            $data['nomor_tagihan'] = $prefix.str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
            $data['created_by'] = Auth::id();
            TagihanServiceKendaraanBatam::create($data);
        });

        return redirect()->route('tagihan-service-kendaraan-batam.index')
            ->with('success', 'Tagihan service kendaraan Batam berhasil ditambahkan.');
    }

    public function edit(TagihanServiceKendaraanBatam $serviceTagihan)
    {
        return view('tagihan-service-kendaraan-batam.edit', ['tagihan' => $serviceTagihan]);
    }

    public function update(Request $request, TagihanServiceKendaraanBatam $serviceTagihan)
    {
        $data = $this->validated($request);
        $data['updated_by'] = Auth::id();
        $serviceTagihan->update($data);

        return redirect()->route('tagihan-service-kendaraan-batam.index')
            ->with('success', 'Tagihan service kendaraan Batam berhasil diperbarui.');
    }

    public function destroy(TagihanServiceKendaraanBatam $serviceTagihan)
    {
        $serviceTagihan->delete();

        return redirect()->route('tagihan-service-kendaraan-batam.index')
            ->with('success', 'Tagihan service kendaraan Batam berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nomor_tagihan' => ['nullable', 'string', 'max:100'],
            'tanggal_service' => ['required', 'date'],
            'nomor_polisi' => ['required', 'string', 'max:30'],
            'bengkel' => ['required', 'string', 'max:255'],
            'rincian_service' => ['required', 'string', 'max:2000'],
            'biaya' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,paid,cancelled'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function previewNomorTagihan(): string
    {
        $prefix = '001'.now()->format('dm');
        $lastNumber = DB::table('tagihan_service_kendaraan_batam_sequences')
            ->where('kode_tanggal_bulan', $prefix)
            ->value('nomor_terakhir') ?? 0;

        return $prefix.str_pad((string) ($lastNumber + 1), 6, '0', STR_PAD_LEFT);
    }
}
