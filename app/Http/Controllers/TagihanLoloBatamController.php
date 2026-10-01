<?php

namespace App\Http\Controllers;

use App\Models\LangsirBatam;
use App\Models\MasterPricelistLoloBatam;
use App\Models\SuratJalanBongkaranBatam;
use App\Models\TagihanLoloBatam;
use App\Models\TagihanLoloBatamItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TagihanLoloBatamController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statusPembayaran = $request->get('status_pembayaran');
        $vendor = $request->get('vendor');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = TagihanLoloBatam::with(['createdBy', 'updatedBy', 'items'])->latest('tanggal_tagihan');

        if ($search) {
            $query->search($search);
        }

        if ($statusPembayaran) {
            $query->where('status_pembayaran', $statusPembayaran);
        }

        if ($vendor) {
            $query->where('vendor', 'like', '%'.$vendor.'%');
        }

        if ($startDate) {
            $query->whereDate('tanggal_tagihan', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('tanggal_tagihan', '<=', $endDate);
        }

        // Summary Statistics
        $totalTagihanCount = TagihanLoloBatam::count();
        $totalNominal = TagihanLoloBatam::sum('total_tagihan');
        $totalLunas = TagihanLoloBatam::where('status_pembayaran', 'Lunas')->sum('total_tagihan');
        $totalBelumLunas = TagihanLoloBatam::where('status_pembayaran', 'Belum Lunas')->sum('total_tagihan');
        $countBelumLunas = TagihanLoloBatam::where('status_pembayaran', 'Belum Lunas')->count();

        $tagihans = $query->paginate(20)->withQueryString();

        $vendors = TagihanLoloBatam::select('vendor')->whereNotNull('vendor')->distinct()->pluck('vendor');

        return view('tagihan-lolo-batam.index', compact(
            'tagihans',
            'search',
            'statusPembayaran',
            'vendor',
            'startDate',
            'endDate',
            'totalTagihanCount',
            'totalNominal',
            'totalLunas',
            'totalBelumLunas',
            'countBelumLunas',
            'vendors'
        ));
    }

    public function create()
    {
        $pricelists = MasterPricelistLoloBatam::aktif()->orderBy('size')->get();

        // Generate automatic invoice number: TLB-YYYYMMDD-XXXX
        $datePrefix = 'TLB-'.date('Ymd');
        $lastInvoice = TagihanLoloBatam::where('nomor_tagihan', 'like', $datePrefix.'%')
            ->orderBy('nomor_tagihan', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNum = (int) substr($lastInvoice->nomor_tagihan, -4);
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        $nomorTagihan = $datePrefix.'-'.$nextNum;

        // Existing vendors from previous invoices
        $vendors = TagihanLoloBatam::select('vendor')->whereNotNull('vendor')->where('vendor', '!=', '')->distinct()->pluck('vendor');

        // Kapal list from recent Bongkaran Batam
        $kapals = SuratJalanBongkaranBatam::select('nama_kapal')->whereNotNull('nama_kapal')->where('nama_kapal', '!=', '')->distinct()->pluck('nama_kapal');

        return view('tagihan-lolo-batam.create', compact('pricelists', 'nomorTagihan', 'vendors', 'kapals'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_tagihan' => 'required|string|unique:tagihan_lolo_batams,nomor_tagihan',
            'tanggal_tagihan' => 'required|date',
            'vendor' => 'nullable|string|max:255',
            'kapal' => 'nullable|string|max:255',
            'voyage' => 'nullable|string|max:255',
            'status_pembayaran' => 'required|in:Belum Lunas,Lunas',
            'tanggal_bayar' => 'nullable|required_if:status_pembayaran,Lunas|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nomor_kontainer' => 'required|string|max:255',
            'items.*.size' => 'nullable|string|max:10',
            'items.*.tipe_kontainer' => 'nullable|string|max:50',
            'items.*.kegiatan' => 'nullable|string|max:255',
            'items.*.sumber_data' => 'nullable|string|in:bongkaran,langsir,manual',
            'items.*.surat_jalan_bongkaran_id' => 'nullable|integer',
            'items.*.langsir_batam_id' => 'nullable|integer',
            'items.*.master_pricelist_lolo_batam_id' => 'nullable|integer',
            'items.*.nomor_surat_jalan' => 'nullable|string|max:255',
            'items.*.tarif' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $totalTagihan = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $subtotal = (float) $item['tarif'] * (int) $item['jumlah'];
                $totalTagihan += $subtotal;

                $itemsData[] = [
                    'sumber_data' => $item['sumber_data'] ?? 'manual',
                    'surat_jalan_bongkaran_id' => ! empty($item['surat_jalan_bongkaran_id']) ? $item['surat_jalan_bongkaran_id'] : null,
                    'langsir_batam_id' => ! empty($item['langsir_batam_id']) ? $item['langsir_batam_id'] : null,
                    'master_pricelist_lolo_batam_id' => ! empty($item['master_pricelist_lolo_batam_id']) ? $item['master_pricelist_lolo_batam_id'] : null,
                    'nomor_surat_jalan' => $item['nomor_surat_jalan'] ?? null,
                    'nomor_kontainer' => strtoupper(trim($item['nomor_kontainer'])),
                    'size' => $item['size'] ?? '20',
                    'tipe_kontainer' => $item['tipe_kontainer'] ?? 'FULL',
                    'kegiatan' => $item['kegiatan'] ?? 'LOLO Batam',
                    'tarif' => $item['tarif'],
                    'jumlah' => $item['jumlah'],
                    'total' => $subtotal,
                    'keterangan' => $item['keterangan'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $tagihan = TagihanLoloBatam::create([
                'nomor_tagihan' => $request->nomor_tagihan,
                'tanggal_tagihan' => $request->tanggal_tagihan,
                'vendor' => $request->vendor,
                'kapal' => $request->kapal,
                'voyage' => $request->voyage,
                'status_pembayaran' => $request->status_pembayaran,
                'tanggal_bayar' => $request->status_pembayaran === 'Lunas' ? $request->tanggal_bayar : null,
                'total_tagihan' => $totalTagihan,
                'keterangan' => $request->keterangan,
                'status_approval' => 'Pending',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            foreach ($itemsData as $row) {
                $row['tagihan_lolo_batam_id'] = $tagihan->id;
                TagihanLoloBatamItem::create($row);
            }

            DB::commit();

            return redirect()->route('tagihan-lolo-batam.show', $tagihan->id)
                ->with('success', 'Tagihan LOLO Batam '.$tagihan->nomor_tagihan.' berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menyimpan Tagihan LOLO Batam: '.$e->getMessage())
                ->withInput();
        }
    }

    public function show(TagihanLoloBatam $tagihanLoloBatam)
    {
        $tagihanLoloBatam->load(['items.pricelistLoloBatam', 'createdBy', 'updatedBy']);

        return view('tagihan-lolo-batam.show', compact('tagihanLoloBatam'));
    }

    public function edit(TagihanLoloBatam $tagihanLoloBatam)
    {
        $tagihanLoloBatam->load('items');
        $pricelists = MasterPricelistLoloBatam::aktif()->orderBy('size')->get();
        $vendors = TagihanLoloBatam::select('vendor')->whereNotNull('vendor')->where('vendor', '!=', '')->distinct()->pluck('vendor');
        $kapals = SuratJalanBongkaranBatam::select('nama_kapal')->whereNotNull('nama_kapal')->where('nama_kapal', '!=', '')->distinct()->pluck('nama_kapal');

        return view('tagihan-lolo-batam.edit', compact('tagihanLoloBatam', 'pricelists', 'vendors', 'kapals'));
    }

    public function update(Request $request, TagihanLoloBatam $tagihanLoloBatam)
    {
        $validator = Validator::make($request->all(), [
            'nomor_tagihan' => 'required|string|unique:tagihan_lolo_batams,nomor_tagihan,'.$tagihanLoloBatam->id,
            'tanggal_tagihan' => 'required|date',
            'vendor' => 'nullable|string|max:255',
            'kapal' => 'nullable|string|max:255',
            'voyage' => 'nullable|string|max:255',
            'status_pembayaran' => 'required|in:Belum Lunas,Lunas',
            'tanggal_bayar' => 'nullable|required_if:status_pembayaran,Lunas|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nomor_kontainer' => 'required|string|max:255',
            'items.*.size' => 'nullable|string|max:10',
            'items.*.tipe_kontainer' => 'nullable|string|max:50',
            'items.*.kegiatan' => 'nullable|string|max:255',
            'items.*.sumber_data' => 'nullable|string|in:bongkaran,langsir,manual',
            'items.*.surat_jalan_bongkaran_id' => 'nullable|integer',
            'items.*.langsir_batam_id' => 'nullable|integer',
            'items.*.master_pricelist_lolo_batam_id' => 'nullable|integer',
            'items.*.nomor_surat_jalan' => 'nullable|string|max:255',
            'items.*.tarif' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $totalTagihan = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $subtotal = (float) $item['tarif'] * (int) $item['jumlah'];
                $totalTagihan += $subtotal;

                $itemsData[] = [
                    'tagihan_lolo_batam_id' => $tagihanLoloBatam->id,
                    'sumber_data' => $item['sumber_data'] ?? 'manual',
                    'surat_jalan_bongkaran_id' => ! empty($item['surat_jalan_bongkaran_id']) ? $item['surat_jalan_bongkaran_id'] : null,
                    'langsir_batam_id' => ! empty($item['langsir_batam_id']) ? $item['langsir_batam_id'] : null,
                    'master_pricelist_lolo_batam_id' => ! empty($item['master_pricelist_lolo_batam_id']) ? $item['master_pricelist_lolo_batam_id'] : null,
                    'nomor_surat_jalan' => $item['nomor_surat_jalan'] ?? null,
                    'nomor_kontainer' => strtoupper(trim($item['nomor_kontainer'])),
                    'size' => $item['size'] ?? '20',
                    'tipe_kontainer' => $item['tipe_kontainer'] ?? 'FULL',
                    'kegiatan' => $item['kegiatan'] ?? 'LOLO Batam',
                    'tarif' => $item['tarif'],
                    'jumlah' => $item['jumlah'],
                    'total' => $subtotal,
                    'keterangan' => $item['keterangan'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $tagihanLoloBatam->update([
                'nomor_tagihan' => $request->nomor_tagihan,
                'tanggal_tagihan' => $request->tanggal_tagihan,
                'vendor' => $request->vendor,
                'kapal' => $request->kapal,
                'voyage' => $request->voyage,
                'status_pembayaran' => $request->status_pembayaran,
                'tanggal_bayar' => $request->status_pembayaran === 'Lunas' ? $request->tanggal_bayar : null,
                'total_tagihan' => $totalTagihan,
                'keterangan' => $request->keterangan,
                'updated_by' => Auth::id(),
            ]);

            // Replace items
            $tagihanLoloBatam->items()->delete();
            foreach ($itemsData as $row) {
                TagihanLoloBatamItem::create($row);
            }

            DB::commit();

            return redirect()->route('tagihan-lolo-batam.show', $tagihanLoloBatam->id)
                ->with('success', 'Tagihan LOLO Batam '.$tagihanLoloBatam->nomor_tagihan.' berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui Tagihan LOLO Batam: '.$e->getMessage())
                ->withInput();
        }
    }

    public function destroy(TagihanLoloBatam $tagihanLoloBatam)
    {
        $nomor = $tagihanLoloBatam->nomor_tagihan;
        $tagihanLoloBatam->delete();

        return redirect()->route('tagihan-lolo-batam.index')
            ->with('success', 'Tagihan LOLO Batam '.$nomor.' berhasil dihapus.');
    }

    public function print(TagihanLoloBatam $tagihanLoloBatam)
    {
        $tagihanLoloBatam->load(['items.pricelistLoloBatam', 'createdBy']);

        return view('tagihan-lolo-batam.print', compact('tagihanLoloBatam'));
    }

    public function export(Request $request)
    {
        $search = $request->get('search');
        $statusPembayaran = $request->get('status_pembayaran');

        $query = TagihanLoloBatam::with(['items', 'createdBy'])->latest('tanggal_tagihan');

        if ($search) {
            $query->search($search);
        }

        if ($statusPembayaran) {
            $query->where('status_pembayaran', $statusPembayaran);
        }

        $tagihans = $query->get();

        $filename = 'tagihan_lolo_batam_'.date('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($tagihans) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($handle, [
                'No',
                'Nomor Tagihan',
                'Tanggal Tagihan',
                'Vendor',
                'Kapal',
                'Voyage',
                'Jumlah Item Kontainer',
                'Total Tagihan (Rp)',
                'Status Pembayaran',
                'Tanggal Bayar',
                'Keterangan',
                'Dibuat Oleh',
            ]);

            foreach ($tagihans as $idx => $tagihan) {
                fputcsv($handle, [
                    $idx + 1,
                    $tagihan->nomor_tagihan,
                    $tagihan->tanggal_tagihan ? $tagihan->tanggal_tagihan->format('d/m/Y') : '-',
                    $tagihan->vendor ?? '-',
                    $tagihan->kapal ?? '-',
                    $tagihan->voyage ?? '-',
                    $tagihan->items->count(),
                    $tagihan->total_tagihan,
                    $tagihan->status_pembayaran,
                    $tagihan->tanggal_bayar ? $tagihan->tanggal_bayar->format('d/m/Y') : '-',
                    $tagihan->keterangan ?? '-',
                    $tagihan->createdBy ? $tagihan->createdBy->name ?? $tagihan->createdBy->username : '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API to search and retrieve unbilled containers with LOLO = 1 from Bongkaran Batam & Langsir Batam
     */
    public function getPendingLolo(Request $request)
    {
        $type = $request->get('type', 'all'); // 'bongkaran', 'langsir', 'all'
        $search = $request->get('search');

        $billedBongkaranIds = TagihanLoloBatamItem::whereNotNull('surat_jalan_bongkaran_id')->pluck('surat_jalan_bongkaran_id')->toArray();
        $billedLangsirIds = TagihanLoloBatamItem::whereNotNull('langsir_batam_id')->pluck('langsir_batam_id')->toArray();

        $results = [];

        if ($type === 'all' || $type === 'bongkaran') {
            $bongkaranQuery = SuratJalanBongkaranBatam::where('menggunakan_lolo', 1)
                ->whereNotIn('id', $billedBongkaranIds);

            if ($search) {
                $bongkaranQuery->where(function ($q) use ($search) {
                    $q->where('no_kontainer', 'like', '%'.$search.'%')
                        ->orWhere('nomor_surat_jalan', 'like', '%'.$search.'%')
                        ->orWhere('nama_kapal', 'like', '%'.$search.'%');
                });
            }

            $bongkarans = $bongkaranQuery->latest('tanggal_surat_jalan')->limit(50)->get();

            foreach ($bongkarans as $b) {
                $results[] = [
                    'sumber_data' => 'bongkaran',
                    'id' => $b->id,
                    'nomor_surat_jalan' => $b->nomor_surat_jalan,
                    'nomor_kontainer' => $b->no_kontainer,
                    'size' => $b->size ?? '20',
                    'tipe_kontainer' => $b->tipe_kontainer ?? ($b->f_e ? strtoupper($b->f_e) : 'FULL'),
                    'kapal' => $b->nama_kapal,
                    'voyage' => $b->no_voyage,
                    'tanggal' => $b->tanggal_surat_jalan ? date('d/m/Y', strtotime($b->tanggal_surat_jalan)) : '-',
                    'kegiatan' => 'LOLO Bongkaran Batam ('.($b->nomor_surat_jalan ?? '-').')',
                ];
            }
        }

        if ($type === 'all' || $type === 'langsir') {
            $langsirQuery = LangsirBatam::where('menggunakan_lolo', 1)
                ->whereNotIn('id', $billedLangsirIds);

            if ($search) {
                $langsirQuery->where(function ($q) use ($search) {
                    $q->where('no_kontainer', 'like', '%'.$search.'%')
                        ->orWhere('no_surat_jalan', 'like', '%'.$search.'%')
                        ->orWhere('no_transaksi', 'like', '%'.$search.'%');
                });
            }

            $langsirs = $langsirQuery->latest('tanggal')->limit(50)->get();

            foreach ($langsirs as $l) {
                $results[] = [
                    'sumber_data' => 'langsir',
                    'id' => $l->id,
                    'nomor_surat_jalan' => $l->no_surat_jalan ?? $l->no_transaksi,
                    'nomor_kontainer' => $l->no_kontainer,
                    'size' => $l->size ?? '20',
                    'tipe_kontainer' => 'FULL',
                    'kapal' => '-',
                    'voyage' => '-',
                    'tanggal' => $l->tanggal ? date('d/m/Y', strtotime($l->tanggal)) : '-',
                    'kegiatan' => 'LOLO Langsir Batam ('.($l->dari ?? '-').' -> '.($l->ke ?? '-').')',
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => count($results),
        ]);
    }
}
