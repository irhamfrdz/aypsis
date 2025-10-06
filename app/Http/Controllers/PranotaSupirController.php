<?php

namespace App\Http\Controllers;

use App\Models\PranotaSupir;
use App\Models\Permohonan;
use App\Models\MasterKegiatan;
use App\Models\NomorTerakhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PranotaSupirController extends Controller
{
    /**
     * Menampilkan form untuk membuat pranota baru.
     */
    public function create(Request $request)
    {
        // Ambil data permohonan yang sudah disetujui oleh kedua sistem approval (Selesai atau Bermasalah) dan belum memiliki pranota
        $permohonans = Permohonan::whereIn('status', ['Selesai', 'Bermasalah'])
            ->where('approved_by_system_1', true)
            ->where('approved_by_system_2', true)
            ->whereDoesntHave('pranotas')
            ->with(['supir', 'pranotas']);
        if ($request->start_date) {
            $permohonans->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $permohonans->whereDate('created_at', '<=', $request->end_date);
        }
        $permohonans = $permohonans->get();

        // Nomor cetakan default 1, bisa diubah via query
        $nomor_cetakan = $request->input('nomor_cetakan', 1);
        // Get next nomor pranota from master nomor terakhir
        $nomorTerakhir = NomorTerakhir::where('modul', 'PMS')->first();
        $nextNumber = $nomorTerakhir ? $nomorTerakhir->nomor_terakhir + 1 : 1;
        $tahun = now()->format('y');
        $bulan = now()->format('m');
        $nomor_pranota_display = "PMS{$nomor_cetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        // Preload a map of kode_kegiatan => nama_kegiatan so the view can display names without queries inside the loop
        $kegiatanMap = MasterKegiatan::pluck('nama_kegiatan', 'kode_kegiatan')->toArray();

        return view('pranota-supir.create', [
            'permohonans' => $permohonans,
            'nomor_pranota_display' => $nomor_pranota_display,
            'nomor_cetakan' => $nomor_cetakan,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'kegiatanMap' => $kegiatanMap,
        ]);
    }

    /**
     * Menampilkan daftar pranota supir.
     */
    public function index(Request $request)
    {
        $query = PranotaSupir::with('permohonans.supir')
            ->latest('tanggal_pranota');

        // Filter berdasarkan pencarian (nomor pranota atau nama supir)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pranota', 'like', "%{$search}%")
                    ->orWhereHas('permohonans.supir', function ($subq) use ($search) {
                        $subq->where('nama_karyawan', 'like', "%{$search}%")
                            ->orWhere('nama_panggilan', 'like', "%{$search}%");
                    });
            });
        }

        // Filter berdasarkan status pembayaran
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        $pranotas = $query->paginate(10)->appends($request->query());

        return view('pranota-supir.index', [
            'pranotas' => $pranotas,
            'search' => $request->search ?? '',
            'status_pembayaran' => $request->status_pembayaran ?? ''
        ]);
    }

    /**
     * Menampilkan detail pranota supir.
     */
    public function show(PranotaSupir $pranotaSupir)
    {
        $pranotaSupir->load('permohonans.supir', 'permohonans.krani', 'permohonans.kontainers');

        return view('pranota-supir.show', compact('pranotaSupir'));
    }

    /**
     * Cetak satu pranota (print-friendly)
     */
    public function print(PranotaSupir $pranotaSupir)
    {
        $pranotaSupir->load('permohonans.supir', 'permohonans.krani', 'permohonans.kontainers');
        return view('pranota-supir.print', compact('pranotaSupir'));
    }

    /**
     * Print multiple pranota berdasarkan rentang tanggal.
     */
    public function printByDate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Ambil semua pranota dalam rentang tanggal
        $pranotas = PranotaSupir::with(['permohonans.supir', 'permohonans.krani', 'permohonans.kontainers'])
            ->whereBetween('tanggal_pranota', [$startDate, $endDate])
            ->orderBy('tanggal_pranota', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($pranotas->isEmpty()) {
            return redirect()->back()->with('warning', 'Tidak ada pranota supir ditemukan dalam rentang tanggal tersebut.');
        }

        // Preload kegiatan map
        $kegiatanMap = MasterKegiatan::pluck('nama_kegiatan', 'kode_kegiatan')->toArray();

        return view('pranota-supir.print-by-date', compact('pranotas', 'kegiatanMap', 'startDate', 'endDate'));
    }

    /**
     * Menyimpan pranota baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'permohonan_ids' => 'required|array',
            'permohonan_ids.*' => 'exists:permohonans,id',
            'catatan' => 'nullable|string',
            'adjustment' => 'nullable|numeric',
            'alasan_adjustment' => 'nullable|string',
        ]);

        // Validasi tambahan: Pastikan semua permohonan masih eligible untuk pranota
        $eligiblePermohonans = Permohonan::whereIn('id', $validatedData['permohonan_ids'])
            ->whereIn('status', ['Selesai', 'Bermasalah'])
            ->where('approved_by_system_1', true)
            ->where('approved_by_system_2', true)
            ->whereDoesntHave('pranotas')
            ->get();

        if ($eligiblePermohonans->count() !== count($validatedData['permohonan_ids'])) {
            return back()->with('error', 'Beberapa memo yang dipilih sudah tidak eligible untuk dibuat pranota. Status mungkin telah berubah atau sudah memiliki pranota.')->withInput();
        }

        $total_biaya_memo = Permohonan::whereIn('id', $validatedData['permohonan_ids'])->sum('total_harga_setelah_adj');

        if ($total_biaya_memo <= 0) {
            return back()->with('error', 'Total biaya memo harus lebih besar dari 0 untuk membuat pranota.')->withInput();
        }

        $adjustment = $validatedData['adjustment'] ?? 0;
        $total_biaya_pranota = $total_biaya_memo + $adjustment;

        $nomor_cetakan = $request->input('nomor_cetakan', 1);

        DB::beginTransaction();
        try {
            // Generate nomor pranota from master nomor terakhir
            $nomorTerakhir = NomorTerakhir::where('modul', 'PMS')->lockForUpdate()->first();
            if (!$nomorTerakhir) {
                return back()->with('error', 'Modul PMS tidak ditemukan di master nomor terakhir.');
            }
            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $tahun = now()->format('y');
            $bulan = now()->format('m');
            $nomor_pranota = "PMS{$nomor_cetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            $nomorTerakhir->nomor_terakhir = $nextNumber;
            $nomorTerakhir->save();

            $pranota = PranotaSupir::create([
                'nomor_pranota' => $nomor_pranota,
                'tanggal_pranota' => now(),
                'total_biaya_memo' => $total_biaya_memo,
                'adjustment' => $adjustment,
                'alasan_adjustment' => $validatedData['alasan_adjustment'],
                'total_biaya_pranota' => $total_biaya_pranota,
                'catatan' => $validatedData['catatan'],
            ]);
            $pranota->permohonans()->sync($validatedData['permohonan_ids']);

            DB::commit();
            return redirect()->route('dashboard')->with('success', 'Pranota berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan pranota: ' . $e->getMessage())->withInput();
        }
    }
}
