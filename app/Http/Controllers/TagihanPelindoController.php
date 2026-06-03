<?php

namespace App\Http\Controllers;

use App\Models\PricelistPelindo;
use App\Models\TagihanPelindo;
use App\Models\TagihanPelindoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TagihanPelindoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status_pembayaran');

        $query = TagihanPelindo::with(['createdBy', 'updatedBy'])->latest();

        if ($search) {
            $query->search($search);
        }

        if ($status) {
            $query->where('status_pembayaran', $status);
        }

        $tagihans = $query->paginate(20)->withQueryString();

        return view('tagihan-pelindo.index', compact('tagihans', 'search', 'status'));
    }

    public function create()
    {
        $pricelists = PricelistPelindo::aktif()->orderBy('kegiatan')->get();

        // Generate automatic invoice number prefix: TPL-YYYYMMDD-XXXX
        $datePrefix = 'TPL-'.date('Ymd');
        $lastInvoice = TagihanPelindo::where('nomor_tagihan', 'like', $datePrefix.'%')
            ->orderBy('nomor_tagihan', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNum = (int) substr($lastInvoice->nomor_tagihan, -4);
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        $nomorTagihan = $datePrefix.'-'.$nextNum;

        return view('tagihan-pelindo.create', compact('pricelists', 'nomorTagihan'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_tagihan' => 'required|string|unique:tagihan_pelindos,nomor_tagihan',
            'tanggal_tagihan' => 'required|date',
            'status_pembayaran' => 'required|in:Belum Lunas,Lunas',
            'tanggal_bayar' => 'nullable|required_if:status_pembayaran,Lunas|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nomor_kontainer' => 'nullable|string',
            'items.*.pricelist_pelindo_id' => 'nullable|exists:pricelist_pelindos,id',
            'items.*.kegiatan' => 'required|string',
            'items.*.ukuran' => 'nullable|string',
            'items.*.status_kontainer' => 'nullable|string',
            'items.*.tarif' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $totalTagihan = 0;
            foreach ($request->items as $item) {
                $totalTagihan += $item['tarif'] * $item['jumlah'];
            }

            $tagihan = TagihanPelindo::create([
                'nomor_tagihan' => $request->nomor_tagihan,
                'tanggal_tagihan' => $request->tanggal_tagihan,
                'status_pembayaran' => $request->status_pembayaran,
                'tanggal_bayar' => $request->status_pembayaran === 'Lunas' ? $request->tanggal_bayar : null,
                'total_tagihan' => $totalTagihan,
                'keterangan' => $request->keterangan,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                TagihanPelindoItem::create([
                    'tagihan_pelindo_id' => $tagihan->id,
                    'nomor_kontainer' => $item['nomor_kontainer'],
                    'pricelist_pelindo_id' => $item['pricelist_pelindo_id'] ?? null,
                    'kegiatan' => $item['kegiatan'],
                    'ukuran' => $item['ukuran'],
                    'status_kontainer' => $item['status_kontainer'] ?? null,
                    'tarif' => $item['tarif'],
                    'jumlah' => $item['jumlah'],
                    'total' => $item['tarif'] * $item['jumlah'],
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('tagihan-pelindo.index')->with('success', 'Tagihan Pelindo berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan: '.$e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $tagihan = TagihanPelindo::with(['items.pricelistPelindo', 'createdBy', 'updatedBy'])->findOrFail($id);

        return view('tagihan-pelindo.show', compact('tagihan'));
    }

    public function edit($id)
    {
        $tagihan = TagihanPelindo::with('items')->findOrFail($id);
        $pricelists = PricelistPelindo::aktif()->orderBy('kegiatan')->get();

        return view('tagihan-pelindo.edit', compact('tagihan', 'pricelists'));
    }

    public function update(Request $request, $id)
    {
        $tagihan = TagihanPelindo::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nomor_tagihan' => 'required|string|unique:tagihan_pelindos,nomor_tagihan,'.$id,
            'tanggal_tagihan' => 'required|date',
            'status_pembayaran' => 'required|in:Belum Lunas,Lunas',
            'tanggal_bayar' => 'nullable|required_if:status_pembayaran,Lunas|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nomor_kontainer' => 'nullable|string',
            'items.*.pricelist_pelindo_id' => 'nullable|exists:pricelist_pelindos,id',
            'items.*.kegiatan' => 'required|string',
            'items.*.ukuran' => 'nullable|string',
            'items.*.status_kontainer' => 'nullable|string',
            'items.*.tarif' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $totalTagihan = 0;
            foreach ($request->items as $item) {
                $totalTagihan += $item['tarif'] * $item['jumlah'];
            }

            $tagihan->update([
                'nomor_tagihan' => $request->nomor_tagihan,
                'tanggal_tagihan' => $request->tanggal_tagihan,
                'status_pembayaran' => $request->status_pembayaran,
                'tanggal_bayar' => $request->status_pembayaran === 'Lunas' ? $request->tanggal_bayar : null,
                'total_tagihan' => $totalTagihan,
                'keterangan' => $request->keterangan,
                'updated_by' => Auth::id(),
            ]);

            // Replace line items
            $tagihan->items()->delete();

            foreach ($request->items as $item) {
                TagihanPelindoItem::create([
                    'tagihan_pelindo_id' => $tagihan->id,
                    'nomor_kontainer' => $item['nomor_kontainer'],
                    'pricelist_pelindo_id' => $item['pricelist_pelindo_id'] ?? null,
                    'kegiatan' => $item['kegiatan'],
                    'ukuran' => $item['ukuran'],
                    'status_kontainer' => $item['status_kontainer'] ?? null,
                    'tarif' => $item['tarif'],
                    'jumlah' => $item['jumlah'],
                    'total' => $item['tarif'] * $item['jumlah'],
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('tagihan-pelindo.index')->with('success', 'Tagihan Pelindo berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan: '.$e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $tagihan = TagihanPelindo::findOrFail($id);

        DB::beginTransaction();
        try {
            $tagihan->items()->delete();
            $tagihan->delete();
            DB::commit();

            return redirect()->route('tagihan-pelindo.index')->with('success', 'Tagihan Pelindo berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }
}
