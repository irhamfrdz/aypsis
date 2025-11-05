<?php

namespace App\Http\Controllers;

use App\Models\SuratJalanBongkaran;
use App\Models\Order;
use App\Models\MasterKapal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuratJalanBongkaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:surat-jalan-bongkaran-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:surat-jalan-bongkaran-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:surat-jalan-bongkaran-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:surat-jalan-bongkaran-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SuratJalanBongkaran::with(['order', 'kapal', 'user']);

        // Filter berdasarkan tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_bongkar', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_bongkar', '<=', $request->end_date);
        }

        // Filter berdasarkan order
        if ($request->filled('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        // Filter berdasarkan kapal
        if ($request->filled('kapal_id')) {
            $query->where('kapal_id', $request->kapal_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('nomor_container', 'like', "%{$search}%")
                  ->orWhere('nomor_seal', 'like', "%{$search}%")
                  ->orWhere('nama_pengirim', 'like', "%{$search}%")
                  ->orWhere('nama_penerima', 'like', "%{$search}%");
            });
        }

        $suratJalanBongkarans = $query->orderBy('created_at', 'desc')->paginate(25);

        // Data untuk filter dropdown
        $orders = Order::orderBy('nomor_order')->get();
        $kapals = MasterKapal::orderBy('nama_kapal')->get();

        return view('surat-jalan-bongkaran.index', compact('suratJalanBongkarans', 'orders', 'kapals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::orderBy('nomor_order')->get();
        $kapals = MasterKapal::orderBy('nama_kapal')->get();
        $users = User::orderBy('name')->get();

        return view('surat-jalan-bongkaran.create', compact('orders', 'kapals', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'kapal_id' => 'nullable|exists:master_kapals,id',
            'nomor_surat_jalan' => 'required|string|max:255|unique:surat_jalan_bongkarans',
            'tanggal_bongkar' => 'required|date',
            'jam_mulai_bongkar' => 'nullable|string',
            'jam_selesai_bongkar' => 'nullable|string',
            'nama_pengirim' => 'nullable|string|max:255',
            'alamat_pengirim' => 'nullable|string',
            'telepon_pengirim' => 'nullable|string|max:50',
            'nama_penerima' => 'nullable|string|max:255',
            'alamat_penerima' => 'nullable|string',
            'telepon_penerima' => 'nullable|string|max:50',
            'jenis_barang' => 'nullable|string|max:255',
            'nama_barang' => 'nullable|string|max:255',
            'jumlah_barang' => 'nullable|numeric',
            'satuan_barang' => 'nullable|string|max:50',
            'berat_barang' => 'nullable|numeric',
            'volume_barang' => 'nullable|numeric',
            'nilai_barang' => 'nullable|numeric',
            'keterangan_barang' => 'nullable|string',
            'nama_supir' => 'nullable|string|max:255',
            'nomor_sim' => 'nullable|string|max:50',
            'telepon_supir' => 'nullable|string|max:50',
            'nama_kenek' => 'nullable|string|max:255',
            'telepon_kenek' => 'nullable|string|max:50',
            'nomor_polisi_kendaraan' => 'nullable|string|max:50',
            'jenis_kendaraan' => 'nullable|string|max:100',
            'nomor_container' => 'nullable|string|max:100',
            'ukuran_container' => 'nullable|string|max:50',
            'jenis_container' => 'nullable|string|max:100',
            'nomor_seal' => 'nullable|string|max:100',
            'kondisi_container' => 'nullable|string|max:100',
            'biaya_bongkar' => 'nullable|numeric',
            'biaya_tambahan' => 'nullable|numeric',
            'total_biaya' => 'nullable|numeric',
            'metode_pembayaran' => 'nullable|string|max:100',
            'status_pembayaran' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
            'catatan_khusus' => 'nullable|string',
            'kondisi_barang' => 'nullable|string|max:100',
            'dokumentasi' => 'nullable|string',
        ]);

        $validatedData['user_id'] = Auth::id();

        try {
            DB::beginTransaction();

            $suratJalanBongkaran = SuratJalanBongkaran::create($validatedData);

            DB::commit();

            return redirect()->route('surat-jalan-bongkaran.show', $suratJalanBongkaran)
                           ->with('success', 'Surat Jalan Bongkaran berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SuratJalanBongkaran $suratJalanBongkaran)
    {
        $suratJalanBongkaran->load(['order', 'kapal', 'user']);
        
        return view('surat-jalan-bongkaran.show', compact('suratJalanBongkaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SuratJalanBongkaran $suratJalanBongkaran)
    {
        $orders = Order::orderBy('nomor_order')->get();
        $kapals = MasterKapal::orderBy('nama_kapal')->get();
        $users = User::orderBy('name')->get();

        return view('surat-jalan-bongkaran.edit', compact('suratJalanBongkaran', 'orders', 'kapals', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SuratJalanBongkaran $suratJalanBongkaran)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'kapal_id' => 'nullable|exists:master_kapals,id',
            'nomor_surat_jalan' => 'required|string|max:255|unique:surat_jalan_bongkarans,nomor_surat_jalan,' . $suratJalanBongkaran->id,
            'tanggal_bongkar' => 'required|date',
            'jam_mulai_bongkar' => 'nullable|string',
            'jam_selesai_bongkar' => 'nullable|string',
            'nama_pengirim' => 'nullable|string|max:255',
            'alamat_pengirim' => 'nullable|string',
            'telepon_pengirim' => 'nullable|string|max:50',
            'nama_penerima' => 'nullable|string|max:255',
            'alamat_penerima' => 'nullable|string',
            'telepon_penerima' => 'nullable|string|max:50',
            'jenis_barang' => 'nullable|string|max:255',
            'nama_barang' => 'nullable|string|max:255',
            'jumlah_barang' => 'nullable|numeric',
            'satuan_barang' => 'nullable|string|max:50',
            'berat_barang' => 'nullable|numeric',
            'volume_barang' => 'nullable|numeric',
            'nilai_barang' => 'nullable|numeric',
            'keterangan_barang' => 'nullable|string',
            'nama_supir' => 'nullable|string|max:255',
            'nomor_sim' => 'nullable|string|max:50',
            'telepon_supir' => 'nullable|string|max:50',
            'nama_kenek' => 'nullable|string|max:255',
            'telepon_kenek' => 'nullable|string|max:50',
            'nomor_polisi_kendaraan' => 'nullable|string|max:50',
            'jenis_kendaraan' => 'nullable|string|max:100',
            'nomor_container' => 'nullable|string|max:100',
            'ukuran_container' => 'nullable|string|max:50',
            'jenis_container' => 'nullable|string|max:100',
            'nomor_seal' => 'nullable|string|max:100',
            'kondisi_container' => 'nullable|string|max:100',
            'biaya_bongkar' => 'nullable|numeric',
            'biaya_tambahan' => 'nullable|numeric',
            'total_biaya' => 'nullable|numeric',
            'metode_pembayaran' => 'nullable|string|max:100',
            'status_pembayaran' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
            'catatan_khusus' => 'nullable|string',
            'kondisi_barang' => 'nullable|string|max:100',
            'dokumentasi' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $suratJalanBongkaran->update($validatedData);

            DB::commit();

            return redirect()->route('surat-jalan-bongkaran.show', $suratJalanBongkaran)
                           ->with('success', 'Surat Jalan Bongkaran berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SuratJalanBongkaran $suratJalanBongkaran)
    {
        try {
            $suratJalanBongkaran->delete();
            
            return redirect()->route('surat-jalan-bongkaran.index')
                           ->with('success', 'Surat Jalan Bongkaran berhasil dihapus.');
                           
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
