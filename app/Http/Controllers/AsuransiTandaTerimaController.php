<?php

namespace App\Http\Controllers;

use App\Models\AsuransiTandaTerima;
use App\Models\VendorAsuransi;
use App\Models\TandaTerima;
use App\Models\TandaTerimaTanpaSuratJalan;
use App\Models\TandaTerimaLcl;
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
            ->select('id', DB::raw("'tt' as type"), 'no_surat_jalan as number', 'tanggal as date', 'pengirim', 'penerima', 'no_kontainer', 'created_at', DB::raw('NULL as deleted_at'))
            ->when($search, function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%");
            });

        // Tanda Terima Tanpa SJ
        $tttsj = DB::table('tanda_terima_tanpa_surat_jalan')
            ->select('id', DB::raw("'tttsj' as type"), 'no_tanda_terima as number', 'tanggal_tanda_terima as date', 'pengirim', 'penerima', 'no_kontainer', 'created_at', DB::raw('NULL as deleted_at'))
            ->when($search, function($q) use ($search) {
                $q->where('no_tanda_terima', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%");
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
                DB::raw('GROUP_CONCAT(tanda_terima_lcl_kontainer_pivot.nomor_kontainer SEPARATOR ", ") as no_kontainer'),
                'tanda_terimas_lcl.created_at', 
                'tanda_terimas_lcl.deleted_at'
            )
            ->whereNull('tanda_terimas_lcl.deleted_at')
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

        return view('asuransi-tanda-terima.create', compact('vendors', 'tandaTerimas', 'tandaTerimaTanpaSjs', 'tandaTerimaLcls', 'selectedType', 'selectedId', 'selectedReceipt'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_asuransi_id' => 'required|exists:vendor_asuransi,id',
            'receipt_type' => 'required|in:tt,tttsj,lcl',
            'receipt_id' => 'required',
            'nomor_polis' => 'required|string|max:255',
            'tanggal_polis' => 'required|date',
            'nilai_pertanggungan' => 'required|numeric|min:0',
            'premi' => 'required|numeric|min:0',
            'asuransi_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'status' => 'required|in:Aktif,Selesai,Batal',
        ]);

        $data = $request->only([
            'vendor_asuransi_id', 'nomor_polis', 'tanggal_polis', 
            'nilai_pertanggungan', 'premi', 'keterangan', 'status'
        ]);

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
        return view('asuransi-tanda-terima.edit', compact('asuransiTandaTerima', 'vendors'));
    }

    public function update(Request $request, AsuransiTandaTerima $asuransiTandaTerima)
    {
        $request->validate([
            'vendor_asuransi_id' => 'required|exists:vendor_asuransi,id',
            'nomor_polis' => 'required|string|max:255',
            'tanggal_polis' => 'required|date',
            'nilai_pertanggungan' => 'required|numeric|min:0',
            'premi' => 'required|numeric|min:0',
            'asuransi_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'status' => 'required|in:Aktif,Selesai,Batal',
        ]);

        $data = $request->only([
            'vendor_asuransi_id', 'nomor_polis', 'tanggal_polis', 
            'nilai_pertanggungan', 'premi', 'keterangan', 'status'
        ]);

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
}
