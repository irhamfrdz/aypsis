<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\NomorTerakhir;
use App\Models\PranotaOngkosTruk;
use App\Models\PranotaOngkosTrukItem;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Models\VendorSupir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PranotaOngkosTrukController extends Controller
{
    public function index(Request $request)
    {
        $query = PranotaOngkosTruk::with(['creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('no_pranota', 'like', "%{$search}%");
        }

        $pranotas = $query->latest()->paginate(10);

        return view('pranota-ongkos-truk.index', compact('pranotas'));
    }

    public function create(Request $request)
    {
        $selectedIds = $request->filled('selected_ids') ? explode(',', $request->selected_ids) : [];
        $sjs = $request->filled('sjs') ? explode(',', $request->sjs) : [];
        $types = $request->filled('types') ? explode(',', $request->types) : [];

        $items = collect();

        foreach ($selectedIds as $index => $id) {
            $type = $types[$index] ?? '';
            $no_sj = $sjs[$index] ?? '';

            $nominal = 0;
            $tanggal = null;
            $supir = null;
            $vendor = null;

            $tujuan = '-';
            if ($type === 'SuratJalan') {
                $sj = SuratJalan::find($id);
                if ($sj) {
                    $nominal = $this->calculateOngkosTruk($sj);
                    $tanggal = $sj->tanggal_surat_jalan;
                    $supir = $sj->supir;
                    $tujuan = $sj->tujuanPengambilanRelation->ke ?? $sj->tujuan_pengambilan ?? '-';
                }
            } elseif ($type === 'SuratJalanBongkaran') {
                $sjb = SuratJalanBongkaran::find($id);
                if ($sjb) {
                    $nominal = $this->calculateOngkosTruk($sjb);
                    $tanggal = $sjb->tanggal_surat_jalan;
                    $supir = $sjb->supir;
                    $tujuan = $sjb->tujuanPengambilanRelation->ke ?? $sjb->tujuan_pengambilan ?? '-';
                }
            }

            if ($id) {
                $items->push([
                    'id' => $id,
                    'no_surat_jalan' => $no_sj,
                    'tanggal' => $tanggal,
                    'nominal' => $nominal,
                    'type' => $type,
                    'supir' => $supir,
                    'tujuan' => $tujuan,
                ]);
            }
        }

        $supirs = Karyawan::where('pekerjaan', 'like', '%Supir%')->get();
        $vendors = VendorSupir::all();

        return view('pranota-ongkos-truk.create', compact('items', 'supirs', 'vendors'));
    }

    private function calculateOngkosTruk($item)
    {
        // Simple implementation mirroring ReportOngkosTrukController
        $ongkosTruk = 0;
        if ($item->tujuanPengambilanRelation) {
            $size = strtolower($item->size ?? '');
            if (str_contains($size, '40')) {
                $ongkosTruk = $item->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0;
            } else {
                $ongkosTruk = $item->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0;
            }
        }
        if ($item->tujuan_pengambilan == 'PULO GADUNG ( BESI SCRAP )') {
            $ongkosTruk = 1050000;
        }

        return $ongkosTruk;
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('PranotaOngkosTrukController@store started', ['payload' => $request->all()]);

        try {
            $request->validate([
                'tanggal_pranota' => 'required|date',
                'items' => 'required|array',
                'items.*.nominal' => 'required|numeric',
                'adjustments' => 'nullable|array',
                'adjustments.*.nominal' => 'required|numeric',
                'adjustments.*.keterangan' => 'nullable|string',
            ]);
            \Illuminate\Support\Facades\Log::info('Validation passed');

            \Illuminate\Support\Facades\DB::beginTransaction();
            \Illuminate\Support\Facades\Log::info('Transaction started');

            // Generate nomor pranota
            $nomorTerakhir = NomorTerakhir::where('modul', 'POT')->lockForUpdate()->first();
            if (! $nomorTerakhir) {
                \Illuminate\Support\Facades\Log::info('NomorTerakhir POT not found, creating new');
                $nomorTerakhir = NomorTerakhir::create(['modul' => 'POT', 'nomor_terakhir' => 0]);
            }
            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $tahun = now()->format('y');
            $bulan = now()->format('m');
            $no_pranota = "POT{$bulan}{$tahun}".str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            \Illuminate\Support\Facades\Log::info('Generated no_pranota: '.$no_pranota);

            $nomorTerakhir->nomor_terakhir = $nextNumber;
            $nomorTerakhir->save();
            \Illuminate\Support\Facades\Log::info('NomorTerakhir updated');

            $adjustments = $request->input('adjustments', []);
            $sumAdjustment = 0;
            if (empty($adjustments) && $request->filled('adjustment')) {
                $sumAdjustment = (float) $request->adjustment;
                $adjustments = [[
                    'nominal' => $sumAdjustment,
                    'keterangan' => $request->keterangan ?? 'Adjustment',
                ]];
            } else {
                foreach ($adjustments as $adj) {
                    $sumAdjustment += (float) ($adj['nominal'] ?? 0);
                }
            }

            $itemsTotal = collect($request->items)->sum('nominal');
            $totalNominal = $itemsTotal + $sumAdjustment;

            $pranota = PranotaOngkosTruk::create([
                'no_pranota' => $no_pranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'adjustment' => $sumAdjustment,
                'adjustments' => $adjustments,
                'total_nominal' => $totalNominal,
                'keterangan' => $request->keterangan,
                'status' => 'submitted',
                'created_by' => Auth::id(),
            ]);
            \Illuminate\Support\Facades\Log::info('Pranota created', ['id' => $pranota->id]);

            foreach ($request->items as $item) {
                // Skip if item doesn't have required data
                if (! isset($item['id']) || ! isset($item['type'])) {
                    \Illuminate\Support\Facades\Log::warning('Skipping item due to missing id or type', ['item' => $item]);

                    continue;
                }

                PranotaOngkosTrukItem::create([
                    'pranota_ongkos_truk_id' => $pranota->id,
                    'surat_jalan_id' => $item['type'] === 'SuratJalan' ? $item['id'] : null,
                    'surat_jalan_bongkaran_id' => $item['type'] === 'SuratJalanBongkaran' ? $item['id'] : null,
                    'no_surat_jalan' => $item['no_surat_jalan'] ?? '-',
                    'tanggal' => isset($item['tanggal']) && $item['tanggal'] !== '-' ? \Carbon\Carbon::createFromFormat('d/M/Y', str_ireplace(['Mei', 'Agu', 'Okt', 'Des'], ['May', 'Aug', 'Oct', 'Dec'], $item['tanggal']))->format('Y-m-d') : null,
                    'nominal' => $item['nominal'] ?? 0,
                    'type' => $item['type'],
                ]);
            }
            \Illuminate\Support\Facades\Log::info('Items created');

            \Illuminate\Support\Facades\DB::commit();
            \Illuminate\Support\Facades\Log::info('Transaction committed');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pranota Ongkos Truk berhasil disimpan.',
                    'redirect_url' => route('pranota-ongkos-truk.show', $pranota->id),
                ]);
            }

            return redirect()->route('pranota-ongkos-truk.show', $pranota->id)->with('success', 'Pranota Ongkos Truk berhasil disimpan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (\Illuminate\Support\Facades\DB::transactionLevel() > 0) {
                \Illuminate\Support\Facades\DB::rollBack();
            }

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: '.implode(', ', collect($e->errors())->flatten()->toArray()),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            if (\Illuminate\Support\Facades\DB::transactionLevel() > 0) {
                \Illuminate\Support\Facades\DB::rollBack();
            }
            \Illuminate\Support\Facades\Log::error('PranotaOngkosTrukController@store error: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan pranota: '.$e->getMessage(),
                    'debug' => [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ],
                ], 500);
            }

            return back()->with('error', 'Gagal menyimpan pranota: '.$e->getMessage());
        }
    }

    public function generateNomorPranota()
    {
        try {
            $nomorTerakhir = NomorTerakhir::where('modul', 'POT')->first();
            $nextNumber = ($nomorTerakhir ? $nomorTerakhir->nomor_terakhir : 0) + 1;
            $tahun = now()->format('y');
            $bulan = now()->format('m');
            $no_pranota = "POT{$bulan}{$tahun}".str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            return response()->json([
                'success' => true,
                'nomor_pranota' => $no_pranota,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPreviewData(Request $request)
    {
        try {
            $selectedIds = array_filter(explode(',', $request->input('selected_ids', '')));
            $types = array_filter(explode(',', $request->input('types', '')));

            $items = collect();
            foreach ($selectedIds as $index => $id) {
                $type = $types[$index] ?? '';
                if ($type === 'SuratJalan') {
                    $sj = SuratJalan::with(['supirKaryawan', 'tujuanPengambilanRelation', 'uangJalan'])->find($id);
                    if ($sj) {
                        $ongkosTruk = $this->calculateOngkosTruk($sj);
                        $uangJalanNominal = $sj->uangJalan ? $sj->uangJalan->jumlah_total : 0;
                        $nominalBersih = (float) $ongkosTruk - (float) $uangJalanNominal;

                        $items->push([
                            'id' => $id,
                            'no_surat_jalan' => $sj->no_surat_jalan,
                            'tanggal' => $sj->tanggal_surat_jalan ? $sj->tanggal_surat_jalan->format('d/M/Y') : '-',
                            'nominal' => $nominalBersih,
                            'type' => $type,
                            'supir' => $sj->supirKaryawan ? ($sj->supirKaryawan->nama_panggilan ?? $sj->supirKaryawan->nama_lengkap) : ($sj->supir ?: '-'),
                            'no_plat' => $sj->no_plat ?: '-',
                            'tujuan' => $sj->tujuanPengambilanRelation->ke ?? $sj->tujuan_pengambilan ?? '-',
                        ]);
                    }
                } elseif ($type === 'SuratJalanBongkaran') {
                    $sjb = SuratJalanBongkaran::with(['supirKaryawan', 'tujuanPengambilanRelation', 'uangJalan'])->find($id);
                    if ($sjb) {
                        $ongkosTruk = $this->calculateOngkosTruk($sjb);
                        $uangJalanNominal = $sjb->uangJalan ? $sjb->uangJalan->jumlah_total : 0;
                        $nominalBersih = (float) $ongkosTruk - (float) $uangJalanNominal;

                        $items->push([
                            'id' => $id,
                            'no_surat_jalan' => $sjb->nomor_surat_jalan,
                            'tanggal' => $sjb->tanggal_surat_jalan ? $sjb->tanggal_surat_jalan->format('d/M/Y') : '-',
                            'nominal' => $nominalBersih,
                            'type' => $type,
                            'supir' => $sjb->supirKaryawan ? ($sjb->supirKaryawan->nama_panggilan ?? $sjb->supirKaryawan->nama_lengkap) : ($sjb->supir ?: '-'),
                            'no_plat' => $sjb->no_plat ?: '-',
                            'tujuan' => $sjb->tujuanPengambilanRelation->ke ?? $sjb->tujuan_pengambilan ?? '-',
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'items' => $items,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses data: '.$e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $pranota = PranotaOngkosTruk::with(['items', 'creator'])->findOrFail($id);

        return view('pranota-ongkos-truk.show', compact('pranota'));
    }

    public function print($id)
    {
        $pranota = PranotaOngkosTruk::with([
            'items.suratJalan.uangJalan',
            'items.suratJalan.supirKaryawan',
            'items.suratJalan.supir2Karyawan',
            'items.suratJalan.kenekKaryawan',
            'items.suratJalanBongkaran.uangJalan',
            'items.suratJalanBongkaran.supirKaryawan',
            'items.suratJalanBongkaran.supir2Karyawan',
            'items.suratJalanBongkaran.kenekKaryawan',
            'creator',
        ])->findOrFail($id);

        return view('pranota-ongkos-truk.print', compact('pranota'));
    }

    public function destroy($id)
    {
        $pranota = PranotaOngkosTruk::findOrFail($id);
        $pranota->delete();

        return redirect()->route('pranota-ongkos-truk.index')->with('success', 'Pranota berhasil dihapus.');
    }

    public function edit($id)
    {
        $pranota = PranotaOngkosTruk::with(['items'])->findOrFail($id);
        $supirs = Karyawan::where('pekerjaan', 'like', '%Supir%')->get();
        $vendors = VendorSupir::all();

        return view('pranota-ongkos-truk.edit', compact('pranota', 'supirs', 'vendors'));
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'tanggal_pranota' => 'required|date',
                'items' => 'required|array',
                'items.*.id' => 'required|integer',
                'items.*.nominal' => 'required|numeric',
                'adjustments' => 'nullable|array',
                'adjustments.*.nominal' => 'required|numeric',
                'adjustments.*.keterangan' => 'nullable|string',
            ]);

            \Illuminate\Support\Facades\DB::beginTransaction();

            $pranota = PranotaOngkosTruk::findOrFail($id);

            $adjustments = $request->input('adjustments', []);
            if (is_string($adjustments)) {
                $adjustments = json_decode($adjustments, true) ?? [];
            }
            $sumAdjustment = 0.0;
            if (empty($adjustments) && $request->filled('adjustment')) {
                $sumAdjustment = (float) $request->adjustment;
                $adjustments = [[
                    'nominal' => $sumAdjustment,
                    'keterangan' => $request->keterangan ?? 'Adjustment',
                ]];
            } else {
                foreach ($adjustments as $adj) {
                    if (is_array($adj)) {
                        $sumAdjustment += (float) ($adj['nominal'] ?? 0);
                    } elseif (is_numeric($adj)) {
                        $sumAdjustment += (float) $adj;
                    }
                }
            }

            $itemsTotal = (float) collect($request->items)->sum('nominal');
            $totalNominal = (float) $itemsTotal + (float) $sumAdjustment;

            $pranota->update([
                'tanggal_pranota' => $request->tanggal_pranota,
                'adjustment' => $sumAdjustment,
                'adjustments' => $adjustments,
                'total_nominal' => $totalNominal,
                'keterangan' => $request->keterangan,
            ]);

            foreach ($request->items as $item) {
                if (isset($item['id'])) {
                    $pranotaItem = PranotaOngkosTrukItem::where('pranota_ongkos_truk_id', $pranota->id)
                        ->where('id', $item['id'])
                        ->first();
                    if ($pranotaItem) {
                        $pranotaItem->update([
                            'nominal' => $item['nominal'] ?? 0,
                        ]);
                    }
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pranota Ongkos Truk berhasil diperbarui.',
                    'redirect_url' => route('pranota-ongkos-truk.show', $pranota->id),
                ]);
            }

            return redirect()->route('pranota-ongkos-truk.show', $pranota->id)->with('success', 'Pranota Ongkos Truk berhasil diperbarui.');
        } catch (\Exception $e) {
            if (\Illuminate\Support\Facades\DB::transactionLevel() > 0) {
                \Illuminate\Support\Facades\DB::rollBack();
            }
            \Illuminate\Support\Facades\Log::error('PranotaOngkosTrukController@update error: '.$e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui pranota: '.$e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Gagal memperbarui pranota: '.$e->getMessage());
        }
    }

    public function export($id)
    {
        $pranota = PranotaOngkosTruk::with(['items.suratJalan.tujuanPengambilanRelation', 'items.suratJalanBongkaran.tujuanPengambilanRelation'])->findOrFail($id);
        $fileName = 'Pranota_Ongkos_Truk_'.$pranota->no_pranota.'.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PranotaOngkosTrukExport($pranota), $fileName);
    }

    public function export2($id)
    {
        $pranota = PranotaOngkosTruk::with(['items.suratJalan.tujuanPengambilanRelation', 'items.suratJalanBongkaran.tujuanPengambilanRelation'])->findOrFail($id);
        $fileName = 'Pranota_Ongkos_Truk_Format2_'.$pranota->no_pranota.'.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PranotaOngkosTrukExport2($pranota), $fileName);
    }
}
