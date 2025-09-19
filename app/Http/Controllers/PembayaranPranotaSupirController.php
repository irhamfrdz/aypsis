<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaSupir;
use App\Models\PranotaSupir;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PembayaranPranotaSupirController extends Controller
{
    // Tampilkan pranota yang belum dibayar
    public function index()
    {
        $pembayarans = PembayaranPranotaSupir::with('pranotas')->latest('tanggal_pembayaran')->get();
        return view('pembayaran-pranota-supir.index', compact('pembayarans'));
    }

    // Tampilkan form pembayaran untuk banyak pranota
    public function create(Request $request)
    {
    // Ambil semua pranota yang belum dibayar, user akan memilih di form
    $pranotas = PranotaSupir::whereDoesntHave('pembayarans')->get();
    $total_tagihan = 0;

    // Ambil data akun COA untuk dropdown bank
    $akunCoa = Coa::orderBy('nama_akun')->get();

    return view('pembayaran-pranota-supir.create', compact('pranotas', 'total_tagihan', 'akunCoa'));
    }

    // Simpan pembayaran ke banyak pranota
    public function store(Request $request)
    {
    Log::debug('PembayaranPranotaSupirController: store entry', ['request' => $request->all()]);

        $validated = $request->validate([
            'pranota_ids' => 'required|array',
            'pranota_ids.*' => 'exists:pranota_supirs,id',
            'nomor_pembayaran' => 'required|string',
            'nomor_cetakan' => 'required|numeric',
            'tanggal_pembayaran' => 'required|date',
            'tanggal_kas' => 'required|string', // Changed from 'date' to 'string' since we use d/M/Y format
            'total_pembayaran' => 'required|numeric',
            'bank' => 'required|string',
            'jenis_transaksi' => 'required|string',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'total_tagihan_setelah_penyesuaian' => 'nullable|numeric',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Convert tanggal_kas from d/M/Y format to Y-m-d for database storage
            $tanggal_kas_db = \Carbon\Carbon::createFromFormat('d/M/Y', $validated['tanggal_kas'])->format('Y-m-d');

            $pembayaran = PembayaranPranotaSupir::create([
                'nomor_pembayaran' => $validated['nomor_pembayaran'],
                'nomor_cetakan' => $validated['nomor_cetakan'],
                'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
                'tanggal_kas' => $tanggal_kas_db,
                'total_pembayaran' => $validated['total_pembayaran'],
                'bank' => $validated['bank'],
                'jenis_transaksi' => $validated['jenis_transaksi'],
                'total_tagihan_penyesuaian' => $validated['total_tagihan_penyesuaian'] ?? null,
                'total_tagihan_setelah_penyesuaian' => $validated['total_tagihan_setelah_penyesuaian'] ?? null,
                'alasan_penyesuaian' => $validated['alasan_penyesuaian'] ?? null,
                // Some environments have `keterangan` as NOT NULL in the DB schema.
                // Default to empty string to avoid integrity constraint violations when not provided.
                'keterangan' => $validated['keterangan'] ?? '',
            ]);
            Log::debug('PembayaranPranotaSupirController: pembayaran_created', array_merge($pembayaran->toArray(), ['pranota_ids' => $validated['pranota_ids']]));
            $pembayaran->pranotas()->sync($validated['pranota_ids']);
            DB::commit();
            return redirect()->route('pembayaran-pranota-supir.index')->with('success', 'Pembayaran berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PembayaranPranotaSupirController: exception', ['message' => $e->getMessage(), 'exception' => $e]);
            return back()->with('error', $e->getMessage());
        }
    }

    // Print single pembayaran
    public function print(PembayaranPranotaSupir $pembayaran)
    {
        $pembayaran->load('pranotas');
        return view('pembayaran-pranota-supir.print', compact('pembayaran'));
    }
}
