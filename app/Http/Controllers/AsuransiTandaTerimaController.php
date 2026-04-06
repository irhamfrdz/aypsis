<?php

namespace App\Http\Controllers;

use App\Models\AsuransiTandaTerima;
use App\Models\VendorAsuransi;
use App\Models\TandaTerima;
use App\Models\TandaTerimaTanpaSuratJalan;
use App\Models\TandaTerimaLcl;
use App\Models\MasterKapal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AsuransiTandaTerimaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        // Tanda Terima Regular
        $tt = DB::table('tanda_terimas')
            ->select(
                'id', 
                DB::raw("'tt' as type"), 
                'no_surat_jalan as number', 
                'tanggal as date', 
                'pengirim', 
                'penerima', 
                'no_kontainer', 
                'nama_barang', 
                'jumlah as kuantitas', 
                'satuan', 
                'created_at', 
                DB::raw('NULL as deleted_at')
            )
            ->whereNotExists(function($q) {
                $q->select(DB::raw(1))
                    ->from('prospek')
                    ->leftJoin('naik_kapal', 'prospek.id', '=', 'naik_kapal.prospek_id')
                    ->leftJoin('bls', 'prospek.id', '=', 'bls.prospek_id')
                    ->whereColumn('prospek.tanda_terima_id', 'tanda_terimas.id')
                    ->where(function($sub) {
                        $sub->where('naik_kapal.sudah_ob', true)
                            ->orWhere('prospek.status', 'sudah_muat')
                            ->orWhereNotNull('bls.id');
                    });
            })
            ->when($search, function($q) use ($search) {
                $q->where(function($sub) use ($search) {
                    $sub->where('no_surat_jalan', 'like', "%{$search}%")
                        ->orWhere('pengirim', 'like', "%{$search}%")
                        ->orWhere('penerima', 'like', "%{$search}%")
                        ->orWhere('no_kontainer', 'like', "%{$search}%");
                });
            });

        // Tanda Terima Tanpa SJ
        $tttsj = DB::table('tanda_terima_tanpa_surat_jalan')
            ->select(
                'id', 
                DB::raw("'tttsj' as type"), 
                'no_tanda_terima as number', 
                'tanggal_tanda_terima as date', 
                'pengirim', 
                'penerima', 
                'no_kontainer', 
                'nama_barang', 
                'jumlah_barang as kuantitas', 
                'satuan_barang as satuan', 
                'created_at', 
                DB::raw('NULL as deleted_at')
            )
            ->whereNotExists(function($q) {
                $q->select(DB::raw(1))
                    ->from('naik_kapal')
                    ->whereColumn('naik_kapal.nomor_kontainer', 'tanda_terima_tanpa_surat_jalan.no_kontainer')
                    ->where('naik_kapal.sudah_ob', true);
            })
            ->whereNotExists(function($q) {
                $q->select(DB::raw(1))
                    ->from('bls')
                    ->whereColumn('bls.nomor_kontainer', 'tanda_terima_tanpa_surat_jalan.no_kontainer');
            })
            ->when($search, function($q) use ($search) {
                $q->where(function($sub) use ($search) {
                    $sub->where('no_tanda_terima', 'like', "%{$search}%")
                        ->orWhere('pengirim', 'like', "%{$search}%")
                        ->orWhere('penerima', 'like', "%{$search}%")
                        ->orWhere('no_kontainer', 'like', "%{$search}%");
                });
            });

        // Tanda Terima LCL
        $lcl = DB::table('tanda_terimas_lcl')
            ->leftJoin('tanda_terima_lcl_kontainer_pivot', 'tanda_terimas_lcl.id', '=', 'tanda_terima_lcl_kontainer_pivot.tanda_terima_lcl_id')
            ->select(
                'tanda_terimas_lcl.id', 
                DB::raw("'lcl' as type"), 
                'nomor_tanda_terima as number', 
                'tanggal_tanda_terima as date', 
                'nama_pengirim as pengirim', 
                'nama_penerima as penerima', 
                DB::raw('GROUP_CONCAT(DISTINCT tanda_terima_lcl_kontainer_pivot.nomor_kontainer SEPARATOR ", ") as no_kontainer'),
                DB::raw('(SELECT GROUP_CONCAT(nama_barang SEPARATOR ", ") FROM tanda_terima_lcl_items WHERE tanda_terima_lcl_id = tanda_terimas_lcl.id) as nama_barang'),
                DB::raw('(SELECT SUM(jumlah) FROM tanda_terima_lcl_items WHERE tanda_terima_lcl_id = tanda_terimas_lcl.id) as kuantitas'),
                DB::raw('(SELECT GROUP_CONCAT(DISTINCT satuan SEPARATOR ", ") FROM tanda_terima_lcl_items WHERE tanda_terima_lcl_id = tanda_terimas_lcl.id) as satuan'),
                'tanda_terimas_lcl.created_at', 
                'tanda_terimas_lcl.deleted_at'
            )
            ->whereNull('tanda_terimas_lcl.deleted_at')
            ->whereNotExists(function($q) {
                $q->select(DB::raw(1))
                    ->from('tanda_terima_lcl_kontainer_pivot')
                    ->join('naik_kapal', 'tanda_terima_lcl_kontainer_pivot.nomor_kontainer', '=', 'naik_kapal.nomor_kontainer')
                    ->whereColumn('tanda_terima_lcl_kontainer_pivot.tanda_terima_lcl_id', 'tanda_terimas_lcl.id')
                    ->where('naik_kapal.sudah_ob', true);
            })
            ->whereNotExists(function($q) {
                $q->select(DB::raw(1))
                    ->from('tanda_terima_lcl_kontainer_pivot')
                    ->join('bls', 'tanda_terima_lcl_kontainer_pivot.nomor_kontainer', '=', 'bls.nomor_kontainer')
                    ->whereColumn('tanda_terima_lcl_kontainer_pivot.tanda_terima_lcl_id', 'tanda_terimas_lcl.id');
            })
            ->groupBy('tanda_terimas_lcl.id', 'type', 'number', 'date', 'pengirim', 'penerima', 'tanda_terimas_lcl.created_at', 'tanda_terimas_lcl.deleted_at')
            ->when($search, function($q) use ($search) {
                $q->where(function($sub) use ($search) {
                    $sub->where('nomor_tanda_terima', 'like', "%{$search}%")
                       ->orWhere('nama_pengirim', 'like', "%{$search}%")
                       ->orWhere('nama_penerima', 'like', "%{$search}%")
                       ->orWhere('tanda_terima_lcl_kontainer_pivot.nomor_kontainer', 'like', "%{$search}%");
                });
            });

        $unionQuery = $tt->union($tttsj)->union($lcl);
        
        $receipts = DB::table(DB::raw("({$unionQuery->toSql()}) as combined_receipts"))
            ->mergeBindings($unionQuery)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Fetch insurance info for these receipts
        $ids = [
            'tt' => collect($receipts->items())->where('type', 'tt')->pluck('id')->toArray(),
            'tttsj' => collect($receipts->items())->where('type', 'tttsj')->pluck('id')->toArray(),
            'lcl' => collect($receipts->items())->where('type', 'lcl')->pluck('id')->toArray(),
        ];

        $insurances = AsuransiTandaTerima::whereIn('tanda_terima_id', $ids['tt'])
            ->orWhereIn('tanda_terima_tanpa_sj_id', $ids['tttsj'])
            ->orWhereIn('tanda_terima_lcl_id', $ids['lcl'])
            ->with('vendorAsuransi')
            ->get()
            ->groupBy(function($item) {
                if ($item->tanda_terima_id) return "tt_{$item->tanda_terima_id}";
                if ($item->tanda_terima_tanpa_sj_id) return "tttsj_{$item->tanda_terima_tanpa_sj_id}";
                if ($item->tanda_terima_lcl_id) return "lcl_{$item->tanda_terima_lcl_id}";
            });

        foreach ($receipts as $receipt) {
            $key = "{$receipt->type}_{$receipt->id}";
            $receipt->insurance = isset($insurances[$key]) ? $insurances[$key]->first() : null;
        }

        return view('asuransi-tanda-terima.index', compact('receipts'));
    }

    public function create(Request $request)
    {
        $vendors = VendorAsuransi::orderBy('nama_asuransi')->get();
        
        $selectedType = $request->type;
        $selectedId = $request->id;
        
        $selectedReceipt = null;
        if ($selectedType && $selectedId) {
            if ($selectedType == 'tt') $selectedReceipt = TandaTerima::find($selectedId);
            elseif ($selectedType == 'tttsj') $selectedReceipt = TandaTerimaTanpaSuratJalan::find($selectedId);
            elseif ($selectedType == 'lcl') $selectedReceipt = TandaTerimaLcl::find($selectedId);
        }

        $tandaTerimas = TandaTerima::latest()->limit(20)->get();
        $tandaTerimaTanpaSjs = TandaTerimaTanpaSuratJalan::latest()->limit(20)->get();
        $tandaTerimaLcls = TandaTerimaLcl::latest()->limit(20)->get();
        $masterKapals = MasterKapal::orderBy('nama_kapal')->get();

        return view('asuransi-tanda-terima.create', compact('vendors', 'tandaTerimas', 'tandaTerimaTanpaSjs', 'tandaTerimaLcls', 'selectedType', 'selectedId', 'selectedReceipt', 'masterKapals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_asuransi_id' => 'required|exists:vendor_asuransi,id',
            'receipt_type' => 'required|in:tt,tttsj,lcl',
            'receipt_id' => 'required',
            'nomor_polis' => 'required|string|max:255',
            'tanggal_polis' => 'required|date',
            'nilai_barang' => 'required|numeric|min:0',
            'asuransi_rate' => 'required|numeric|min:0',
            'asuransi_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->only([
            'vendor_asuransi_id', 'nomor_polis', 'tanggal_polis', 'keterangan',
            'nomor_urut', 'nama_kapal', 'nomor_voyage'
        ]);
        
        $vendor = VendorAsuransi::find($request->vendor_asuransi_id);
        $rate = $request->asuransi_rate ?? ($vendor->tarif ?? 0);
        
        $data['nilai_pertanggungan'] = $request->nilai_barang;
        $data['asuransi_rate'] = $rate;
        $data['premi'] = $request->nilai_barang * ($rate / 100);
        $data['grand_total'] = $data['premi'];

        // Map receipt type to column
        if ($request->receipt_type == 'tt') {
            $data['tanda_terima_id'] = $request->receipt_id;
        } elseif ($request->receipt_type == 'tttsj') {
            $data['tanda_terima_tanpa_sj_id'] = $request->receipt_id;
        } elseif ($request->receipt_type == 'lcl') {
            $data['tanda_terima_lcl_id'] = $request->receipt_id;
        }

        if ($request->hasFile('asuransi_file')) {
            $path = $request->file('asuransi_file')->store('asuransi_tanda_terima', 'public');
            $data['asuransi_path'] = $path;
        }

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        AsuransiTandaTerima::create($data);

        return redirect()->route('asuransi-tanda-terima.index')->with('success', 'Data asuransi berhasil disimpan.');
    }

    public function show(AsuransiTandaTerima $asuransiTandaTerima)
    {
        $asuransiTandaTerima->load(['vendorAsuransi', 'tandaTerima', 'tandaTerimaTanpaSj', 'tandaTerimaLcl', 'creator', 'updater']);
        return view('asuransi-tanda-terima.show', compact('asuransiTandaTerima'));
    }

    public function edit(AsuransiTandaTerima $asuransiTandaTerima)
    {
        $vendors = VendorAsuransi::orderBy('nama_asuransi')->get();
        $masterKapals = MasterKapal::orderBy('nama_kapal')->get();
        return view('asuransi-tanda-terima.edit', compact('asuransiTandaTerima', 'vendors', 'masterKapals'));
    }

    public function update(Request $request, AsuransiTandaTerima $asuransiTandaTerima)
    {
        $request->validate([
            'vendor_asuransi_id' => 'required|exists:vendor_asuransi,id',
            'nomor_polis' => 'required|string|max:255',
            'tanggal_polis' => 'required|date',
            'nilai_barang' => 'required|numeric|min:0',
            'asuransi_rate' => 'required|numeric|min:0',
            'asuransi_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->only([
            'vendor_asuransi_id', 'nomor_polis', 'tanggal_polis', 'keterangan',
            'nomor_urut', 'nama_kapal', 'nomor_voyage'
        ]);

        $vendor = VendorAsuransi::find($request->vendor_asuransi_id);
        $rate = $request->asuransi_rate ?? ($vendor->tarif ?? 0);

        $data['nilai_pertanggungan'] = $request->nilai_barang;
        $data['asuransi_rate'] = $rate;
        $data['premi'] = $request->nilai_barang * ($rate / 100);
        $data['grand_total'] = $data['premi'];

        if ($request->hasFile('asuransi_file')) {
            // Delete old file
            if ($asuransiTandaTerima->asuransi_path) {
                Storage::disk('public')->delete($asuransiTandaTerima->asuransi_path);
            }
            $path = $request->file('asuransi_file')->store('asuransi_tanda_terima', 'public');
            $data['asuransi_path'] = $path;
        }

        $data['updated_by'] = Auth::id();

        $asuransiTandaTerima->update($data);

        return redirect()->route('asuransi-tanda-terima.index')->with('success', 'Data asuransi berhasil diperbarui.');
    }

    public function destroy(AsuransiTandaTerima $asuransiTandaTerima)
    {
        if ($asuransiTandaTerima->asuransi_path) {
            Storage::disk('public')->delete($asuransiTandaTerima->asuransi_path);
        }
        $asuransiTandaTerima->delete();
        return redirect()->route('asuransi-tanda-terima.index')->with('success', 'Data asuransi berhasil dihapus.');
    }

    public function getReceiptDetails($type, $id)
    {
        $details = [
            'no_kontainer' => '-',
            'no_surat_jalan' => '-',
            'nama_barang' => '-',
            'jumlah_barang' => '-',
            'satuan' => '-',
            'nomor_urut' => '-',
            'nama_kapal' => '-',
            'nomor_voyage' => '-',
        ];

        if ($type == 'tt') {
            $tt = TandaTerima::find($id);
            if ($tt) {
                $details['no_kontainer'] = $tt->no_kontainer ?? '-';
                $details['no_surat_jalan'] = $tt->no_surat_jalan ?? '-';
                $details['nama_barang'] = is_array($tt->nama_barang) ? implode(', ', $tt->nama_barang) : ($tt->nama_barang ?? '-');
                $details['jumlah_barang'] = (string)($tt->jumlah ?? '-');
                $details['satuan'] = $tt->satuan ?? '-';
                
                // Get ship info from Tanda Terima or related Prospek
                $details['nama_kapal'] = $tt->estimasi_nama_kapal ?? '-';
                $prospek = $tt->prospeks()->first();
                if ($prospek) {
                    $details['nomor_voyage'] = $prospek->nomor_voyage ?? $prospek->voyage ?? '-';
                    if ($details['nama_kapal'] == '-') {
                        $details['nama_kapal'] = $prospek->nama_kapal ?? '-';
                    }
                }
            }
        } elseif ($type == 'tttsj') {
            $tttsj = TandaTerimaTanpaSuratJalan::find($id);
            if ($tttsj) {
                $details['no_kontainer'] = $tttsj->no_kontainer ?? '-';
                $details['no_surat_jalan'] = $tttsj->nomor_surat_jalan_customer ?? '-';
                $details['nama_barang'] = $tttsj->nama_barang ?? '-';
                $details['jumlah_barang'] = (string)($tttsj->jumlah_barang ?? '-');
                $details['satuan'] = $tttsj->satuan_barang ?? '-';
            }
        } elseif ($type == 'lcl') {
            $lcl = TandaTerimaLcl::with(['items', 'kontainerPivot'])->find($id);
            if ($lcl) {
                $details['no_kontainer'] = $lcl->nomor_kontainer ?? '-';
                $details['no_surat_jalan'] = $lcl->no_surat_jalan_customer ?? '-';
                $details['nama_barang'] = $lcl->items->pluck('nama_barang')->filter()->unique()->implode(', ') ?: '-';
                $details['jumlah_barang'] = (string)($lcl->items->sum('jumlah') ?: '-');
                $details['satuan'] = $lcl->items->pluck('satuan')->filter()->unique()->implode(', ') ?: '-';
            }
        }

        return response()->json($details);
    }
}
