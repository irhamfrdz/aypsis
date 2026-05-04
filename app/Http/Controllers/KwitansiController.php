<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kwitansi;
use App\Models\KwitansiDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KwitansiController extends Controller
{
    public function index()
    {
        $kwitansis = Kwitansi::orderBy('created_at', 'desc')->get();
        return view('kwitansi.index', compact('kwitansis'));
    }

    public function create()
    {
        // Auto-generate Kwt No
        $latestKwitansi = Kwitansi::orderBy('id', 'desc')->first();
        $nextId = $latestKwitansi ? $latestKwitansi->id + 1 : 1;
        $kwtNo = 'KWT-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('kwitansi.create', compact('kwtNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kwt_no' => 'required|unique:kwitansis,kwt_no',
            'pelanggan_kode' => 'nullable|string',
            'pelanggan_nama' => 'nullable|string',
            'tgl_inv' => 'nullable|date',
            'details' => 'required|array|min:1',
            'details.*.item_description' => 'required|string'
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
            ]);

            if ($request->has('details') && is_array($request->details)) {
                foreach ($request->details as $detail) {
                    KwitansiDetail::create([
                        'kwitansi_id' => $kwitansi->id,
                        'item_kode' => $detail['item_kode'] ?? null,
                        'item_description' => $detail['item_description'] ?? null,
                        'qty' => $detail['qty'] ?? 0,
                        'unit_price' => str_replace(',', '', $detail['unit_price'] ?? 0),
                        'amount' => str_replace(',', '', $detail['amount'] ?? 0),
                        'no_bl' => $detail['no_bl'] ?? null,
                        'no_sj' => $detail['no_sj'] ?? null,
                        'dept' => $detail['dept'] ?? null,
                        'proyek' => $detail['proyek'] ?? null,
                        'sn' => $detail['sn'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('kwitansi.index')->with('success', 'Kwitansi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan Kwitansi: ' . $e->getMessage());
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
        return view('kwitansi.edit', compact('kwitansi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kwt_no' => 'required|unique:kwitansis,kwt_no,' . $id,
            'pelanggan_kode' => 'nullable|string',
            'pelanggan_nama' => 'nullable|string',
            'tgl_inv' => 'nullable|date',
            'details' => 'required|array|min:1',
            'details.*.item_description' => 'required|string'
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
            ]);

            // Delete old details
            $kwitansi->details()->delete();

            // Re-create details
            if ($request->has('details') && is_array($request->details)) {
                foreach ($request->details as $detail) {
                    KwitansiDetail::create([
                        'kwitansi_id' => $kwitansi->id,
                        'item_kode' => $detail['item_kode'] ?? null,
                        'item_description' => $detail['item_description'] ?? null,
                        'qty' => $detail['qty'] ?? 0,
                        'unit_price' => str_replace(',', '', $detail['unit_price'] ?? 0),
                        'amount' => str_replace(',', '', $detail['amount'] ?? 0),
                        'no_bl' => $detail['no_bl'] ?? null,
                        'no_sj' => $detail['no_sj'] ?? null,
                        'dept' => $detail['dept'] ?? null,
                        'proyek' => $detail['proyek'] ?? null,
                        'sn' => $detail['sn'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('kwitansi.index')->with('success', 'Kwitansi berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengupdate Kwitansi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $kwitansi = Kwitansi::findOrFail($id);
            $kwitansi->delete();
            return redirect()->route('kwitansi.index')->with('success', 'Kwitansi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus Kwitansi: ' . $e->getMessage());
        }
    }
}
