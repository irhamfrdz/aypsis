<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Term;
use App\Models\Pengirim;
use App\Models\JenisBarang;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['term', 'pengirim', 'jenisBarang']);

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nomor_order', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('tujuan_kirim', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('tujuan_ambil', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('no_tiket_do', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhereHas('pengirim', function ($query) use ($searchTerm) {
                      $query->where('nama_pengirim', 'LIKE', '%' . $searchTerm . '%');
                  });
            });
        }

        $orders = $query->latest()->paginate(15);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $terms = Term::where('status', 'active')->get();
        $pengirims = Pengirim::where('status', 'active')->get();
        $jenisBarangs = JenisBarang::where('status', 'active')->get();

        return view('orders.create', compact('terms', 'pengirims', 'jenisBarangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_order' => 'required|string|unique:orders,nomor_order',
            'tanggal_order' => 'required|date',
            'tujuan_kirim' => 'required|string|max:255',
            'tujuan_ambil' => 'required|string|max:255',
            'size_kontainer' => 'required|string|max:255',
            'unit_kontainer' => 'required|integer|min:1',
            'tipe_kontainer' => 'required|in:fcl,lcl,cargo,fcl_plus',
            'tanggal_pickup' => 'nullable|date',
            'no_tiket_do' => 'nullable|string|max:255',
            'term_id' => 'nullable|exists:terms,id',
            'pengirim_id' => 'nullable|exists:pengirims,id',
            'jenis_barang_id' => 'nullable|exists:jenis_barangs,id',
            'status' => 'required|in:draft,confirmed,processing,completed,cancelled',
            'catatan' => 'nullable|string',
            'ftz03_option' => 'nullable|in:exclude,include,none',
            'sppb_option' => 'nullable|in:exclude,include,none',
            'buruh_bongkar_option' => 'nullable|in:exclude,include,none',
        ]);

        $data = $request->all();

        // Convert radio button options to boolean fields
        $data['exclude_ftz03'] = $request->ftz03_option === 'exclude';
        $data['include_ftz03'] = $request->ftz03_option === 'include';
        $data['exclude_sppb'] = $request->sppb_option === 'exclude';
        $data['include_sppb'] = $request->sppb_option === 'include';
        $data['exclude_buruh_bongkar'] = $request->buruh_bongkar_option === 'exclude';
        $data['include_buruh_bongkar'] = $request->buruh_bongkar_option === 'include';

        // Remove the radio button fields from data
        unset($data['ftz03_option'], $data['sppb_option'], $data['buruh_bongkar_option']);

        Order::create($data);

        return redirect()->route('orders.index')->with('success', 'Order berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with(['term', 'pengirim', 'jenisBarang'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $order = Order::findOrFail($id);
        $terms = Term::where('status', 'active')->get();
        $pengirims = Pengirim::where('status', 'active')->get();
        $jenisBarangs = JenisBarang::where('status', 'active')->get();

        return view('orders.edit', compact('order', 'terms', 'pengirims', 'jenisBarangs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'nomor_order' => 'required|string|unique:orders,nomor_order,' . $id,
            'tanggal_order' => 'required|date',
            'tujuan_kirim' => 'required|string|max:255',
            'tujuan_ambil' => 'required|string|max:255',
            'size_kontainer' => 'required|string|max:255',
            'unit_kontainer' => 'required|integer|min:1',
            'tipe_kontainer' => 'required|in:fcl,lcl,cargo,fcl_plus',
            'tanggal_pickup' => 'nullable|date',
            'no_tiket_do' => 'nullable|string|max:255',
            'term_id' => 'nullable|exists:terms,id',
            'pengirim_id' => 'nullable|exists:pengirims,id',
            'jenis_barang_id' => 'nullable|exists:jenis_barangs,id',
            'status' => 'required|in:draft,confirmed,processing,completed,cancelled',
            'catatan' => 'nullable|string',
            'ftz03_option' => 'nullable|in:exclude,include,none',
            'sppb_option' => 'nullable|in:exclude,include,none',
            'buruh_bongkar_option' => 'nullable|in:exclude,include,none',
        ]);

        $data = $request->all();

        // Convert radio button options to boolean fields
        $data['exclude_ftz03'] = $request->ftz03_option === 'exclude';
        $data['include_ftz03'] = $request->ftz03_option === 'include';
        $data['exclude_sppb'] = $request->sppb_option === 'exclude';
        $data['include_sppb'] = $request->sppb_option === 'include';
        $data['exclude_buruh_bongkar'] = $request->buruh_bongkar_option === 'exclude';
        $data['include_buruh_bongkar'] = $request->buruh_bongkar_option === 'include';

        // Remove the radio button fields from data
        unset($data['ftz03_option'], $data['sppb_option'], $data['buruh_bongkar_option']);

        $order->update($data);

        return redirect()->route('orders.index')->with('success', 'Order berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order berhasil dihapus.');
    }
}
