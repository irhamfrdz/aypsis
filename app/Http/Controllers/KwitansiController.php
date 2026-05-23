<?php

namespace App\Http\Controllers;

use App\Models\Kwitansi;
use App\Models\KwitansiDetail;
use App\Models\Coa;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KwitansiController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
    }
    public function index(Request $request)
    {
        $namaKapal = $request->get('nama_kapal');
        $noVoyage = $request->get('no_voyage');
        $search = $request->get('search');

        // If no filters, redirect to select ship page
        if (! $namaKapal || ! $noVoyage) {
            return redirect()->route('kwitansi.select-ship');
        }

        $kwitansis = Kwitansi::orderBy('created_at', 'desc')->get();

        // Fetch manifests for the "Draft" tab
        $manifestQuery = \App\Models\Manifest::query();

        if ($namaKapal) {
            $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
            $normalizedKapal = str_replace('  ', ' ', $normalizedKapal);
            $manifestQuery->whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal]);
        }

        if ($noVoyage) {
            $manifestQuery->where('no_voyage', trim($noVoyage));
        }

        if ($search) {
            $manifestQuery->where(function ($q) use ($search) {
                $q->where('nomor_kontainer', 'like', "%{$search}%")
                    ->orWhere('nomor_bl', 'like', "%{$search}%")
                    ->orWhere('nomor_manifest', 'like', "%{$search}%");
            });
        }

        $manifests = $manifestQuery->orderBy('created_at', 'desc')->get();

        return view('kwitansi.index', compact('kwitansis', 'manifests', 'namaKapal', 'noVoyage', 'search'));
    }

    public function selectShip(Request $request)
    {
        // Get list of ships from manifests table
        $shipsFromManifests = \App\Models\Manifest::whereNotNull('nama_kapal')
            ->select('nama_kapal')
            ->distinct()
            ->pluck('nama_kapal');

        // Get ships from naik_kapal table as well
        $shipsFromNaikKapal = \App\Models\NaikKapal::whereNotNull('nama_kapal')
            ->select('nama_kapal')
            ->distinct()
            ->pluck('nama_kapal');

        // Merge and get unique ship names
        $shipNames = $shipsFromManifests->merge($shipsFromNaikKapal)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        // Convert to objects for view compatibility
        $ships = $shipNames->map(function ($name) {
            return (object) ['nama_kapal' => $name];
        });

        return view('kwitansi.select-ship', compact('ships'));
    }

    public function getVoyagesByShip($namaKapal)
    {
        $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
        $normalizedKapal = str_replace('  ', ' ', $normalizedKapal);

        $voyagesFromManifests = \App\Models\Manifest::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->whereNotNull('no_voyage')
            ->select('no_voyage')
            ->distinct()
            ->pluck('no_voyage');

        $voyagesFromNaikKapal = \App\Models\NaikKapal::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->whereNotNull('no_voyage')
            ->select('no_voyage')
            ->distinct()
            ->pluck('no_voyage');

        $voyages = $voyagesFromManifests->merge($voyagesFromNaikKapal)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return response()->json(['voyages' => $voyages]);
    }

    public function create(Request $request)
    {
        // Auto-generate Kwt No
        $latestKwitansi = Kwitansi::orderBy('id', 'desc')->first();
        $nextId = $latestKwitansi ? $latestKwitansi->id + 1 : 1;
        $kwtNo = 'KWT-'.date('Ymd').'-'.str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $manifest = null;
        if ($request->has('manifest_id')) {
            $manifest = \App\Models\Manifest::find($request->manifest_id);
        }

        $akunPiutangList = \App\Models\Coa::orderBy('kode_nomor')->get();

        return view('kwitansi.create', compact('kwtNo', 'manifest', 'akunPiutangList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kwt_no' => 'required|unique:kwitansis,kwt_no',
            'pelanggan_kode' => 'nullable|string',
            'pelanggan_nama' => 'nullable|string',
            'tgl_inv' => 'nullable|date',
            'details' => 'required|array|min:1',
            'details.*.item_description' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $kwitansi = Kwitansi::create([
                'pelanggan_kode' => $request->pelanggan_kode,
                'pelanggan_nama' => $request->pelanggan_nama,
                'terima_dari' => $request->terima_dari,
                'kirim_ke' => $request->kirim_ke,
                'no_po' => $request->no_po,
                'kwt_no' => $request->kwt_no,
                'tgl_inv' => $request->tgl_inv,
                'tgl_kirim' => $request->tgl_kirim,
                'fob' => $request->fob,
                'syarat_pembayaran' => $request->syarat_pembayaran,
                'pengirim' => $request->pengirim,
                'penjual' => $request->penjual,
                'keterangan' => $request->keterangan,
                'akun_piutang' => $request->akun_piutang,
                'sub_total' => $request->sub_total ?? 0,
                'discount_persen' => $request->discount_persen ?? 0,
                'discount_nominal' => $request->discount_nominal ?? 0,
                'biaya_kirim' => $request->biaya_kirim ?? 0,
                'total_invoice' => $request->total_invoice ?? 0,
                'kena_pajak' => $request->has('kena_pajak'),
                'termasuk_pajak' => $request->has('termasuk_pajak'),
            ]);

            if ($request->has('details') && is_array($request->details)) {
                foreach ($request->details as $detail) {
                    $detailAmount = str_replace(',', '', $detail['amount'] ?? 0);

                    KwitansiDetail::create([
                        'kwitansi_id' => $kwitansi->id,
                        'item_kode' => $detail['item_kode'] ?? null,
                        'item_description' => $detail['item_description'] ?? null,
                        'qty' => $detail['qty'] ?? 0,
                        'unit_price' => str_replace(',', '', $detail['unit_price'] ?? 0),
                        'amount' => $detailAmount,
                        'no_bl' => $detail['no_bl'] ?? null,
                        'no_sj' => $detail['no_sj'] ?? null,
                    ]);

                    // Credit ke akun COA yang dipilih di item description
                    $itemDesc = $detail['item_description'] ?? null;
                    if ($itemDesc && Coa::where('nama_akun', $itemDesc)->exists()) {
                        $this->coaTransactionService->recordTransaction(
                            $itemDesc,
                            0,
                            (float) $detailAmount,
                            $request->tgl_inv ?? date('Y-m-d'),
                            $kwitansi->kwt_no,
                            'penjualan',
                            $detail['item_kode']
                                ? "{$itemDesc} ({$detail['item_kode']}) - {$kwitansi->kwt_no}"
                                : "{$itemDesc} - {$kwitansi->kwt_no}"
                        );
                    }
                }
            }

            // Debit ke akun piutang
            if ($request->akun_piutang && Coa::where('nama_akun', $request->akun_piutang)->exists()) {
                $totalInvoice = str_replace(',', '', $request->total_invoice ?? 0);
                $this->coaTransactionService->recordTransaction(
                    $request->akun_piutang,
                    (float) $totalInvoice,
                    0,
                    $request->tgl_inv ?? date('Y-m-d'),
                    $kwitansi->kwt_no,
                    'penjualan',
                    "Piutang {$kwitansi->kwt_no} - {$kwitansi->pelanggan_nama}"
                );
            }

            DB::commit();

            return redirect()->route('kwitansi.show', $kwitansi->id)->with('success', 'Kwitansi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal menyimpan Kwitansi: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $kwitansi = Kwitansi::with('details')->findOrFail($id);

        return view('kwitansi.show', compact('kwitansi'));
    }

    public function edit($id)
    {
        $kwitansi = Kwitansi::with('details')->findOrFail($id);
        $akunPiutangList = \App\Models\Coa::orderBy('kode_nomor')->get();

        return view('kwitansi.edit', compact('kwitansi', 'akunPiutangList'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kwt_no' => 'required|unique:kwitansis,kwt_no,'.$id,
            'pelanggan_kode' => 'nullable|string',
            'pelanggan_nama' => 'nullable|string',
            'tgl_inv' => 'nullable|date',
            'details' => 'required|array|min:1',
            'details.*.item_description' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $kwitansi = Kwitansi::findOrFail($id);
            $kwitansi->update([
                'pelanggan_kode' => $request->pelanggan_kode,
                'pelanggan_nama' => $request->pelanggan_nama,
                'terima_dari' => $request->terima_dari,
                'kirim_ke' => $request->kirim_ke,
                'no_po' => $request->no_po,
                'kwt_no' => $request->kwt_no,
                'tgl_inv' => $request->tgl_inv,
                'tgl_kirim' => $request->tgl_kirim,
                'fob' => $request->fob,
                'syarat_pembayaran' => $request->syarat_pembayaran,
                'pengirim' => $request->pengirim,
                'penjual' => $request->penjual,
                'keterangan' => $request->keterangan,
                'akun_piutang' => $request->akun_piutang,
                'sub_total' => $request->sub_total ?? 0,
                'discount_persen' => $request->discount_persen ?? 0,
                'discount_nominal' => $request->discount_nominal ?? 0,
                'biaya_kirim' => $request->biaya_kirim ?? 0,
                'total_invoice' => $request->total_invoice ?? 0,
                'kena_pajak' => $request->has('kena_pajak'),
                'termasuk_pajak' => $request->has('termasuk_pajak'),
            ]);

            // Delete old COA transactions
            $this->coaTransactionService->deleteTransactionByReference($kwitansi->kwt_no);

            // Delete old details
            $kwitansi->details()->delete();

            // Re-create details
            if ($request->has('details') && is_array($request->details)) {
                foreach ($request->details as $detail) {
                    $detailAmount = str_replace(',', '', $detail['amount'] ?? 0);

                    KwitansiDetail::create([
                        'kwitansi_id' => $kwitansi->id,
                        'item_kode' => $detail['item_kode'] ?? null,
                        'item_description' => $detail['item_description'] ?? null,
                        'qty' => $detail['qty'] ?? 0,
                        'unit_price' => str_replace(',', '', $detail['unit_price'] ?? 0),
                        'amount' => $detailAmount,
                        'no_bl' => $detail['no_bl'] ?? null,
                        'no_sj' => $detail['no_sj'] ?? null,
                    ]);

                    // Credit ke akun COA yang dipilih di item description
                    $itemDesc = $detail['item_description'] ?? null;
                    if ($itemDesc && Coa::where('nama_akun', $itemDesc)->exists()) {
                        $this->coaTransactionService->recordTransaction(
                            $itemDesc,
                            0,
                            (float) $detailAmount,
                            $request->tgl_inv ?? date('Y-m-d'),
                            $kwitansi->kwt_no,
                            'penjualan',
                            $detail['item_kode']
                                ? "{$itemDesc} ({$detail['item_kode']}) - {$kwitansi->kwt_no}"
                                : "{$itemDesc} - {$kwitansi->kwt_no}"
                        );
                    }
                }
            }

            // Debit ke akun piutang
            if ($request->akun_piutang && Coa::where('nama_akun', $request->akun_piutang)->exists()) {
                $totalInvoice = str_replace(',', '', $request->total_invoice ?? 0);
                $this->coaTransactionService->recordTransaction(
                    $request->akun_piutang,
                    (float) $totalInvoice,
                    0,
                    $request->tgl_inv ?? date('Y-m-d'),
                    $kwitansi->kwt_no,
                    'penjualan',
                    "Piutang {$kwitansi->kwt_no} - {$kwitansi->pelanggan_nama}"
                );
            }

            DB::commit();

            return redirect()->route('kwitansi.show', $kwitansi->id)->with('success', 'Kwitansi berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal mengupdate Kwitansi: '.$e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $kwitansi = Kwitansi::findOrFail($id);

            // Hapus COA transactions
            $this->coaTransactionService->deleteTransactionByReference($kwitansi->kwt_no);

            $kwitansi->delete();

            DB::commit();

            return redirect()->route('kwitansi.index')->with('success', 'Kwitansi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menghapus Kwitansi: '.$e->getMessage());
        }
    }

    public function print($id)
    {
        $kwitansi = Kwitansi::with('details')->findOrFail($id);
        $terbilang = $this->terbilang($kwitansi->total_invoice);

        return view('kwitansi.print', compact('kwitansi', 'terbilang'));
    }

    private function terbilang($number)
    {
        $number = abs($number);
        $words = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        $temp = '';

        if ($number < 12) {
            $temp = ' '.$words[$number];
        } elseif ($number < 20) {
            $temp = $this->terbilang($number - 10).' belas';
        } elseif ($number < 100) {
            $temp = $this->terbilang($number / 10).' puluh'.$this->terbilang($number % 10);
        } elseif ($number < 200) {
            $temp = ' seratus'.$this->terbilang($number - 100);
        } elseif ($number < 1000) {
            $temp = $this->terbilang($number / 100).' ratus'.$this->terbilang($number % 100);
        } elseif ($number < 2000) {
            $temp = ' seribu'.$this->terbilang($number - 1000);
        } elseif ($number < 1000000) {
            $temp = $this->terbilang($number / 1000).' ribu'.$this->terbilang($number % 1000);
        } elseif ($number < 1000000000) {
            $temp = $this->terbilang($number / 1000000).' juta'.$this->terbilang($number % 1000000);
        } elseif ($number < 1000000000000) {
            $temp = $this->terbilang($number / 1000000000).' milyar'.$this->terbilang(fmod($number, 1000000000));
        } elseif ($number < 1000000000000000) {
            $temp = $this->terbilang($number / 1000000000000).' trilyun'.$this->terbilang(fmod($number, 1000000000000));
        }

        return trim($temp);
    }
}
