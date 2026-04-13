<?php

namespace App\Http\Controllers;

use App\Models\AsuransiTandaTerimaBatch;
use App\Models\AsuransiTandaTerimaBatchItem;
use App\Models\AsuransiTandaTerima;
use App\Models\VendorAsuransi;
use App\Models\TandaTerima;
use App\Models\TandaTerimaTanpaSuratJalan;
use App\Models\TandaTerimaLcl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AsuransiTandaTerimaBatchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // We'll use the same permissions as regular insurance for now, or new ones if preferred.
        // The user asked for a "new" menu, so maybe new permissions are better.
        $this->middleware('can:asuransi-tanda-terima-multi-view')->only(['index', 'show']);
        $this->middleware('can:asuransi-tanda-terima-multi-create')->only(['create', 'store']);
        $this->middleware('can:asuransi-tanda-terima-multi-update')->only(['edit', 'update']);
        $this->middleware('can:asuransi-tanda-terima-multi-delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = AsuransiTandaTerimaBatch::with(['vendorAsuransi', 'creator'])
            ->withCount('items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nomor_polis', 'like', "%{$search}%")
                ->orWhereHas('vendorAsuransi', function($q) use ($search) {
                    $q->where('nama_asuransi', 'like', "%{$search}%");
                });
        }

        $batches = $query->latest()->paginate(15);

        return view('asuransi-tanda-terima-multi.index', compact('batches'));
    }

    public function create(Request $request)
    {
        $vendors = VendorAsuransi::orderBy('nama_asuransi')->get();
        
        // Get receipts that are not yet insured in BOTH systems
        $receipts = $this->getAvailableReceipts($request);

        return view('asuransi-tanda-terima-multi.create', compact('vendors', 'receipts'));
    }

    private function getAvailableReceipts(Request $request)
    {
        // 1. Regular Tanda Terima
        $tt = DB::table('tanda_terimas')
            ->leftJoin('surat_jalans', 'tanda_terimas.surat_jalan_id', '=', 'surat_jalans.id')
            ->whereNotExists(function($q) {
                $q->select(DB::raw(1))
                    ->from('asuransi_tanda_terimas')
                    ->whereColumn('asuransi_tanda_terimas.tanda_terima_id', 'tanda_terimas.id');
            })
            ->whereNotExists(function($q) {
                $q->select(DB::raw(1))
                    ->from('asuransi_tanda_terima_batch_items')
                    ->whereColumn('asuransi_tanda_terima_batch_items.tanda_terima_id', 'tanda_terimas.id');
            })
            ->select(
                'tanda_terimas.id',
                DB::raw("'tt' as type"),
                'tanda_terimas.no_surat_jalan as number',
                'tanda_terimas.tanggal as date',
                'tanda_terimas.pengirim',
                'tanda_terimas.penerima',
                'tanda_terimas.no_kontainer',
                DB::raw('COALESCE(tanda_terimas.nama_barang, surat_jalans.jenis_barang) as name'),
                DB::raw('COALESCE(tanda_terimas.jumlah, surat_jalans.jumlah_kontainer) as qty'),
                'tanda_terimas.satuan'
            );

        // 2. Tanpa SJ
        $tttsj = DB::table('tanda_terima_tanpa_surat_jalan')
            ->whereNotExists(function($q) {
                $q->select(DB::raw(1))
                    ->from('asuransi_tanda_terimas')
                    ->whereColumn('asuransi_tanda_terimas.tanda_terima_tanpa_sj_id', 'tanda_terima_tanpa_surat_jalan.id');
            })
            ->whereNotExists(function($q) {
                $q->select(DB::raw(1))
                    ->from('asuransi_tanda_terima_batch_items')
                    ->whereColumn('asuransi_tanda_terima_batch_items.tanda_terima_tanpa_sj_id', 'tanda_terima_tanpa_surat_jalan.id');
            })
            ->select(
                'id',
                DB::raw("'tttsj' as type"),
                'no_tanda_terima as number',
                'tanggal_tanda_terima as date',
                'pengirim',
                'penerima',
                'no_kontainer',
                'nama_barang as name',
                'jumlah_barang as qty',
                'satuan_barang as satuan'
            );

        // 3. LCL
        $lcl = DB::table('tanda_terimas_lcl')
            ->whereNull('deleted_at')
            ->whereNotExists(function($q) {
                $q->select(DB::raw(1))
                    ->from('asuransi_tanda_terimas')
                    ->whereColumn('asuransi_tanda_terimas.tanda_terima_lcl_id', 'tanda_terimas_lcl.id');
            })
            ->whereNotExists(function($q) {
                $q->select(DB::raw(1))
                    ->from('asuransi_tanda_terima_batch_items')
                    ->whereColumn('asuransi_tanda_terima_batch_items.tanda_terima_lcl_id', 'tanda_terimas_lcl.id');
            })
            ->select(
                'id',
                DB::raw("'lcl' as type"),
                'nomor_tanda_terima as number',
                'tanggal_tanda_terima as date',
                'nama_pengirim as pengirim',
                'nama_penerima as penerima',
                DB::raw('NULL as no_kontainer'),
                DB::raw('NULL as name'),
                DB::raw('0 as qty'),
                DB::raw('NULL as satuan')
            );

        return $tt->union($tttsj)->union($lcl)->orderBy('date', 'desc')->limit(100)->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_asuransi_id' => 'required',
            'tanggal_polis' => 'required|date',
            'selected_receipts' => 'required|array|min:1',
            'asuransi_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $batch = new AsuransiTandaTerimaBatch();
            $batch->nomor_polis = $request->nomor_polis;
            $batch->tanggal_polis = $request->tanggal_polis;
            $batch->vendor_asuransi_id = $request->vendor_asuransi_id;
            $batch->asuransi_rate = $request->asuransi_rate ?? 0;
            $batch->biaya_admin = $request->biaya_admin ?? 0;
            $batch->keterangan = $request->keterangan;
            $batch->created_by = Auth::id();
            $batch->updated_by = Auth::id();

            if ($request->hasFile('asuransi_file')) {
                $batch->asuransi_path = $request->file('asuransi_file')->store('asuransi_batch', 'public');
            }

            $batch->save();

            $totalNP = 0;
            foreach ($request->selected_receipts as $receiptKey) {
                // Key format: type_id
                list($type, $id) = explode('_', $receiptKey);
                
                $batchItem = new AsuransiTandaTerimaBatchItem();
                $batchItem->batch_id = $batch->id;
                $batchItem->receipt_type = $type;
                
                // Get nilai pertanggungan from request if provided per item, 
                // but for now let's assume it's entered in the form
                $nilaiPertanggungan = $request->input("nilai_pertanggungan.{$receiptKey}", 0);
                $batchItem->nilai_pertanggungan = $nilaiPertanggungan;
                
                if ($type == 'tt') $batchItem->tanda_terima_id = $id;
                elseif ($type == 'tttsj') $batchItem->tanda_terima_tanpa_sj_id = $id;
                elseif ($type == 'lcl') $batchItem->tanda_terima_lcl_id = $id;
                
                $batchItem->save();
                $totalNP += $nilaiPertanggungan;
            }

            $batch->total_nilai_pertanggungan = $totalNP;
            $batch->premi = $totalNP * ($batch->asuransi_rate / 100);
            $batch->grand_total = $batch->premi + $batch->biaya_admin;
            $batch->save();

            DB::commit();
            return redirect()->route('asuransi-tanda-terima-multi.index')->with('success', 'Batch Asuransi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function show(AsuransiTandaTerimaBatch $asuransiTandaTerimaMulti)
    {
        $asuransiTandaTerimaMulti->load(['items.tandaTerima', 'items.tandaTerimaTanpaSj', 'items.tandaTerimaLcl', 'vendorAsuransi', 'creator']);
        return view('asuransi-tanda-terima-multi.show', ['batch' => $asuransiTandaTerimaMulti]);
    }

    public function edit(AsuransiTandaTerimaBatch $asuransiTandaTerimaMulti)
    {
        $asuransiTandaTerimaMulti->load(['items.tandaTerima', 'items.tandaTerimaTanpaSj', 'items.tandaTerimaLcl', 'vendorAsuransi']);
        $vendors = VendorAsuransi::orderBy('nama_asuransi')->get();
        return view('asuransi-tanda-terima-multi.edit', ['batch' => $asuransiTandaTerimaMulti, 'vendors' => $vendors]);
    }

    public function update(Request $request, AsuransiTandaTerimaBatch $asuransiTandaTerimaMulti)
    {
        $request->validate([
            'vendor_asuransi_id' => 'required',
            'tanggal_polis' => 'required|date',
        ]);

        $asuransiTandaTerimaMulti->update([
            'nomor_polis' => $request->nomor_polis,
            'tanggal_polis' => $request->tanggal_polis,
            'vendor_asuransi_id' => $request->vendor_asuransi_id,
            'asuransi_rate' => $request->asuransi_rate,
            'biaya_admin' => $request->biaya_admin,
            'keterangan' => $request->keterangan,
            'updated_by' => Auth::id(),
        ]);

        // Recalculate totals
        $asuransiTandaTerimaMulti->premi = $asuransiTandaTerimaMulti->total_nilai_pertanggungan * ($asuransiTandaTerimaMulti->asuransi_rate / 100);
        $asuransiTandaTerimaMulti->grand_total = $asuransiTandaTerimaMulti->premi + $asuransiTandaTerimaMulti->biaya_admin;
        $asuransiTandaTerimaMulti->save();

        return redirect()->route('asuransi-tanda-terima-multi.index')->with('success', 'Batch Asuransi berhasil diperbarui.');
    }

    public function destroy(AsuransiTandaTerimaBatch $asuransiTandaTerimaMulti)
    {
        $asuransiTandaTerimaMulti->delete();
        return redirect()->route('asuransi-tanda-terima-multi.index')->with('success', 'Batch Asuransi berhasil dihapus.');
    }
}
