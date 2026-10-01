<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistLoloBatam;
use App\Models\TagihanLoloBatam;
use App\Models\TagihanLoloBatamItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PranotaLoloBatamController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statusPembayaran = $request->get('status_pembayaran');
        $tipeOperator = $request->get('tipe_operator');
        $vendor = $request->get('vendor');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = TagihanLoloBatam::with(['createdBy', 'updatedBy', 'items', 'operatorKaryawan'])->latest('tanggal_tagihan');

        if ($search) {
            $query->search($search);
        }

        if ($statusPembayaran) {
            $query->where('status_pembayaran', $statusPembayaran);
        }

        if ($tipeOperator) {
            $query->where('tipe_operator', $tipeOperator);
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

        $totalPranotaCount = TagihanLoloBatam::count();
        $totalNominal = TagihanLoloBatam::sum('total_tagihan');
        $totalLunas = TagihanLoloBatam::where('status_pembayaran', 'Lunas')->sum('total_tagihan');
        $totalBelumLunas = TagihanLoloBatam::where('status_pembayaran', 'Belum Lunas')->sum('total_tagihan');
        $countBelumLunas = TagihanLoloBatam::where('status_pembayaran', 'Belum Lunas')->count();
        $countLunas = TagihanLoloBatam::where('status_pembayaran', 'Lunas')->count();

        $pranotas = $query->paginate(20)->withQueryString();
        $vendors = TagihanLoloBatam::select('vendor')->whereNotNull('vendor')->where('vendor', '!=', '')->distinct()->pluck('vendor');

        return view('pranota-lolo-batam.index', compact(
            'pranotas',
            'search',
            'statusPembayaran',
            'tipeOperator',
            'vendor',
            'startDate',
            'endDate',
            'totalPranotaCount',
            'totalNominal',
            'totalLunas',
            'totalBelumLunas',
            'countBelumLunas',
            'countLunas',
            'vendors'
        ));
    }

    public function show(TagihanLoloBatam $pranotaLoloBatam)
    {
        $pranotaLoloBatam->load(['createdBy', 'updatedBy', 'operatorKaryawan', 'items.suratJalanBongkaran', 'items.langsirBatam']);

        return view('pranota-lolo-batam.show', [
            'pranota' => $pranotaLoloBatam,
            'tagihanLoloBatam' => $pranotaLoloBatam,
        ]);
    }

    public function edit(TagihanLoloBatam $pranotaLoloBatam)
    {
        $pranotaLoloBatam->load(['createdBy', 'updatedBy', 'operatorKaryawan', 'items']);
        $pricelists = MasterPricelistLoloBatam::aktif()->orderBy('size')->get();
        $vendors = TagihanLoloBatam::select('vendor')->whereNotNull('vendor')->where('vendor', '!=', '')->distinct()->pluck('vendor');
        $kapals = \App\Models\SuratJalanBongkaranBatam::select('nama_kapal')->whereNotNull('nama_kapal')->where('nama_kapal', '!=', '')->distinct()->pluck('nama_kapal');

        $karyawanOperators = \Illuminate\Support\Facades\Schema::hasTable('karyawans')
            ? \App\Models\Karyawan::whereNull('tanggal_berhenti')
                ->where(function ($q) {
                    $q->whereIn('pekerjaan', ['OPERATOR FORKLIFT', 'OPERATOR CRANE / ALAT BERAT'])
                        ->orWhere('pekerjaan', 'like', '%FORKLIFT%')
                        ->orWhere('pekerjaan', 'like', '%CRANE%')
                        ->orWhere('pekerjaan', 'like', '%ALAT BERAT%');
                })
                ->orderBy('nama_lengkap')
                ->get(['id', 'nama_lengkap', 'nama_panggilan', 'divisi', 'pekerjaan'])
            : collect();

        return view('pranota-lolo-batam.edit', [
            'pranota' => $pranotaLoloBatam,
            'tagihanLoloBatam' => $pranotaLoloBatam,
            'pricelists' => $pricelists,
            'vendors' => $vendors,
            'kapals' => $kapals,
            'karyawanOperators' => $karyawanOperators,
        ]);
    }

    public function update(Request $request, TagihanLoloBatam $pranotaLoloBatam)
    {
        $validator = Validator::make($request->all(), [
            'nomor_tagihan' => 'required|string|unique:tagihan_lolo_batams,nomor_tagihan,'.$pranotaLoloBatam->id,
            'tanggal_tagihan' => 'required|date',
            'vendor' => 'nullable|string|max:255',
            'tipe_operator' => 'nullable|in:AYP,VENDOR',
            'operator_karyawan_id' => 'nullable|exists:karyawans,id',
            'operator' => 'nullable|string|max:255',
            'kapal' => 'nullable|string|max:255',
            'voyage' => 'nullable|string|max:255',
            'status_pembayaran' => 'required|in:Belum Lunas,Lunas',
            'tanggal_bayar' => 'nullable|required_if:status_pembayaran,Lunas|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|integer',
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
            $submittedItemIds = [];

            $tipeOperator = $request->tipe_operator ?: 'AYP';
            $operatorKaryawanId = $request->operator_karyawan_id;
            $operatorName = $request->operator;

            if ($tipeOperator === 'AYP') {
                if ($operatorKaryawanId) {
                    $karyawan = \App\Models\Karyawan::find($operatorKaryawanId);
                    if ($karyawan) {
                        $operatorName = $karyawan->nama_lengkap;
                    }
                }
            } else {
                $operatorKaryawanId = null;
                if (empty($operatorName)) {
                    $operatorName = $request->vendor ?: 'Vendor';
                }
            }

            foreach ($request->items as $item) {
                $subtotal = (float) $item['tarif'] * (int) $item['jumlah'];
                $totalTagihan += $subtotal;

                $itemData = [
                    'tagihan_lolo_batam_id' => $pranotaLoloBatam->id,
                    'sumber_data' => $item['sumber_data'] ?? 'manual',
                    'surat_jalan_bongkaran_id' => ! empty($item['surat_jalan_bongkaran_id']) ? $item['surat_jalan_bongkaran_id'] : null,
                    'langsir_batam_id' => ! empty($item['langsir_batam_id']) ? $item['langsir_batam_id'] : null,
                    'master_pricelist_lolo_batam_id' => ! empty($item['master_pricelist_lolo_batam_id']) ? $item['master_pricelist_lolo_batam_id'] : null,
                    'nomor_surat_jalan' => $item['nomor_surat_jalan'] ?? null,
                    'nomor_kontainer' => strtoupper(trim($item['nomor_kontainer'])),
                    'size' => $item['size'] ?? '20',
                    'tipe_kontainer' => $item['tipe_kontainer'] ?? 'FULL',
                    'kegiatan' => $item['kegiatan'] ?? 'LOLO Batam',
                    'tipe_operator' => $tipeOperator,
                    'operator' => $operatorName,
                    'operator_karyawan_id' => $operatorKaryawanId,
                    'tarif' => $item['tarif'],
                    'jumlah' => $item['jumlah'],
                    'total' => $subtotal,
                    'keterangan' => $item['keterangan'] ?? null,
                ];

                if (! empty($item['id'])) {
                    $existingItem = TagihanLoloBatamItem::where('tagihan_lolo_batam_id', $pranotaLoloBatam->id)->find($item['id']);
                    if ($existingItem) {
                        $existingItem->update($itemData);
                        $submittedItemIds[] = $existingItem->id;
                    }
                } else {
                    $newItem = TagihanLoloBatamItem::create($itemData);
                    $submittedItemIds[] = $newItem->id;
                }
            }

            TagihanLoloBatamItem::where('tagihan_lolo_batam_id', $pranotaLoloBatam->id)
                ->whereNotIn('id', $submittedItemIds)
                ->delete();

            $pranotaLoloBatam->update([
                'nomor_tagihan' => $request->nomor_tagihan,
                'tanggal_tagihan' => $request->tanggal_tagihan,
                'vendor' => $request->vendor,
                'tipe_operator' => $tipeOperator,
                'operator' => $operatorName,
                'operator_karyawan_id' => $operatorKaryawanId,
                'kapal' => $request->kapal,
                'voyage' => $request->voyage,
                'status_pembayaran' => $request->status_pembayaran,
                'tanggal_bayar' => $request->status_pembayaran === 'Lunas' ? $request->tanggal_bayar : null,
                'total_tagihan' => $totalTagihan,
                'keterangan' => $request->keterangan,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('pranota-lolo-batam.show', $pranotaLoloBatam->id)
                ->with('success', 'Pranota LOLO Batam '.$pranotaLoloBatam->nomor_tagihan.' berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal memperbarui pranota: '.$e->getMessage())->withInput();
        }
    }

    public function destroy(TagihanLoloBatam $pranotaLoloBatam)
    {
        DB::beginTransaction();
        try {
            $nomor = $pranotaLoloBatam->nomor_tagihan;
            $pranotaLoloBatam->items()->delete();
            $pranotaLoloBatam->delete();

            DB::commit();

            return redirect()->route('pranota-lolo-batam.index')
                ->with('success', 'Pranota LOLO Batam '.$nomor.' berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal menghapus pranota: '.$e->getMessage());
        }
    }

    public function print(TagihanLoloBatam $pranotaLoloBatam)
    {
        $pranotaLoloBatam->load(['createdBy', 'updatedBy', 'operatorKaryawan', 'items']);

        return view('pranota-lolo-batam.print', [
            'pranota' => $pranotaLoloBatam,
            'tagihanLoloBatam' => $pranotaLoloBatam,
        ]);
    }

    public function export(Request $request)
    {
        $query = TagihanLoloBatam::with(['items', 'createdBy', 'operatorKaryawan'])->latest('tanggal_tagihan');

        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
        if ($request->filled('vendor')) {
            $query->where('vendor', 'like', '%'.$request->vendor.'%');
        }
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_tagihan', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_tagihan', '<=', $request->end_date);
        }

        $tagihans = $query->get();

        $filename = 'pranota_lolo_batam_'.date('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($tagihans) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($file, [
                'No. Pranota',
                'Tanggal',
                'Vendor / Depo',
                'Tipe Operator',
                'Operator',
                'Nama Kapal',
                'Voyage',
                'Jumlah Kontainer',
                'Total Tagihan (Rp)',
                'Status Pembayaran',
                'Tanggal Bayar',
                'Keterangan',
                'Dibuat Oleh',
            ]);

            foreach ($tagihans as $t) {
                $opName = $t->tipe_operator === 'AYP'
                    ? ($t->operator ?: ($t->operatorKaryawan->nama_lengkap ?? '-'))
                    : ($t->operator ?: ($t->vendor ?: '-'));

                fputcsv($file, [
                    $t->nomor_tagihan,
                    $t->tanggal_tagihan ? $t->tanggal_tagihan->format('Y-m-d') : '',
                    $t->vendor ?? '',
                    $t->tipe_operator ?? 'AYP',
                    $opName,
                    $t->kapal ?? '',
                    $t->voyage ?? '',
                    $t->items->count(),
                    $t->total_tagihan,
                    $t->status_pembayaran,
                    $t->tanggal_bayar ? $t->tanggal_bayar->format('Y-m-d') : '',
                    $t->keterangan ?? '',
                    $t->createdBy->name ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
