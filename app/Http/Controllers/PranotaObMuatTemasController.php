<?php

namespace App\Http\Controllers;

use App\Models\NaikKapal;
use App\Models\PranotaObItem;
use App\Models\PranotaObMuatTemas;
use App\Models\TagihanOb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PranotaObMuatTemasController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('pranota-ob.index', $request->only('search'));
    }

    public function generateNomor()
    {
        $prefix = 'PMT-'.now()->format('m-y').'-';
        $last = PranotaObMuatTemas::where('nomor_pranota', 'like', $prefix.'%')->orderByDesc('nomor_pranota')->value('nomor_pranota');

        return response()->json([
            'success' => true,
            'nomor_pranota' => $prefix.str_pad(((int) substr($last ?? '', -6)) + 1, 6, '0', STR_PAD_LEFT),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_pranota' => ['required', 'string', 'regex:/^PMT-\d{2}-\d{2}-\d{6}$/', 'unique:pranota_ob_muat_temas,nomor_pranota'],
            'tanggal_ob' => 'required|date',
            'nama_kapal' => 'required|string|max:255',
            'no_voyage' => 'required|string|max:255',
            'nomor_accurate' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'adjustment' => 'nullable|numeric',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|distinct|exists:naik_kapal,id',
            'items.*.type' => 'required|in:naik_kapal',
        ]);

        $pranota = DB::transaction(function () use ($validated) {
            $ids = collect($validated['items'])->pluck('id');
            $records = NaikKapal::whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get();
            if ($records->count() !== $ids->count()) {
                throw ValidationException::withMessages(['items' => 'Data kontainer berubah. Muat ulang halaman dan pilih kembali kontainer.']);
            }
            $tagihans = TagihanOb::with(['pranotaMuatTemasItem', 'pranotaObItem', 'pranotaObAntarGudangItem'])
                ->whereIn('naik_kapal_id', $ids)->where('kegiatan', 'MUAT TEMAS')
                ->orderBy('id')->lockForUpdate()->get()->groupBy('naik_kapal_id');
            $snapshots = [];
            foreach ($records as $record) {
                $matches = $tagihans->get($record->id, collect());
                $tagihan = $matches->last();
                $normalized = fn ($value) => strtoupper(preg_replace('/\s+/', ' ', str_replace('.', '', trim($value))));
                if (! $record->sudah_ob || ! $tagihan || $matches->count() !== 1
                    || $normalized($record->nama_kapal) !== $normalized($validated['nama_kapal'])
                    || trim($record->no_voyage) !== trim($validated['no_voyage'])) {
                    throw ValidationException::withMessages(['items' => 'Kontainer '.$record->nomor_kontainer.' harus memiliki satu tagihan OB Muat Temas yang sesuai kapal dan voyage.']);
                }
                $inRegularPranota = PranotaObItem::where('item_type', NaikKapal::class)->where('item_id', $record->id)->exists();
                if ($tagihan->pranotaMuatTemasItem || $tagihan->pranotaObItem || $tagihan->pranotaObAntarGudangItem || $inRegularPranota) {
                    throw ValidationException::withMessages(['items' => 'Kontainer '.$record->nomor_kontainer.' sudah masuk pranota.']);
                }
                if ($tagihan->biaya < 0) {
                    throw ValidationException::withMessages(['items' => 'Biaya OB Muat Temas tidak boleh negatif.']);
                }
                $destination = $record->ke;
                if (preg_match('/^OB Muat Temas - (.+) - Pricelist #\d+$/u', $tagihan->keterangan ?? '', $destinationMatches)) {
                    $destination = $destinationMatches[1];
                }
                $snapshots[] = [
                    'tagihan_ob_id' => $tagihan->id,
                    'snapshot' => [
                        'tanggal_ob' => $tagihan->tanggal_ob,
                        'created_at' => $tagihan->created_at->toIso8601String(),
                        'nomor_kontainer' => $tagihan->nomor_kontainer,
                        'nama_supir' => $tagihan->nama_supir,
                        'barang' => $tagihan->barang,
                        'size_kontainer' => $tagihan->size_kontainer,
                        'status_kontainer' => $tagihan->status_kontainer,
                        'biaya' => $tagihan->biaya,
                        'nomor_surat_jalan' => $tagihan->nomor_surat_jalan,
                        'gudang_asal_id' => $tagihan->gudang_asal_id,
                        'tujuan_gudang' => $destination,
                        'is_ckls_mobil_panjang' => $tagihan->is_ckls_mobil_panjang,
                    ],
                ];
            }
            $nominal = round(collect($snapshots)->sum('snapshot.biaya'), 2);
            $adjustment = round($validated['adjustment'] ?? 0, 2);
            if ($nominal + $adjustment < 0) {
                throw ValidationException::withMessages(['adjustment' => 'Total pranota tidak boleh negatif.']);
            }
            $pranota = PranotaObMuatTemas::create([
                'nomor_pranota' => $validated['nomor_pranota'],
                'tanggal_pranota' => $validated['tanggal_ob'],
                'nama_kapal' => $validated['nama_kapal'],
                'no_voyage' => $validated['no_voyage'],
                'nomor_accurate' => $validated['nomor_accurate'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'nominal' => $nominal,
                'adjustment' => $adjustment,
                'grand_total' => $nominal + $adjustment,
                'created_by' => Auth::id(),
            ]);
            $pranota->items()->createMany($snapshots);

            return $pranota;
        });

        return response()->json([
            'success' => true,
            'message' => 'Pranota OB Muat Temas '.$pranota->nomor_pranota.' berhasil dibuat.',
            'redirect_url' => route('pranota-ob.muat-temas.show', $pranota),
        ]);
    }

    public function show(PranotaObMuatTemas $pranota)
    {
        $pranota->load(['creator', 'items']);

        return view('pranota-ob.show-muat-temas', compact('pranota'));
    }

    public function print(PranotaObMuatTemas $pranota)
    {
        $pranota->load(['creator', 'items']);
        $judulPranota = 'PRANOTA OB MUAT TEMAS';

        return view('tagihan-ob.pranota-print-antar-gudang', compact('pranota', 'judulPranota'));
    }
}
