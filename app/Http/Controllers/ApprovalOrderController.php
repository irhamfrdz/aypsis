<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ApprovalOrderController extends Controller
{
    /**
     * Display a listing of approval orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['pengirim', 'jenisBarang', 'term']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_order', 'like', "%{$search}%")
                  ->orWhere('tujuan_ambil', 'like', "%{$search}%")
                  ->orWhere('tujuan_kirim', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_order', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_order', '<=', $request->end_date);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Pagination
        $perPage = $request->get('per_page', 50);
        $orders = $query->paginate($perPage)->withQueryString();

        return view('approval-order.index', compact('orders'));
    }

    /**
     * Show the form for creating a new approval.
     */
    public function create()
    {
        // Get orders yang belum ada term-nya
        $orders = Order::with(['pengirim'])
            ->whereDoesntHave('term')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('approval-order.create', compact('orders'));
    }

    /**
     * Store a newly created approval (add term to order).
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'term_name' => 'required|string|max:255',
            'term_days' => 'required|integer|min:0',
            'description' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Create term
            $term = Term::create([
                'name' => $request->term_name,
                'days' => $request->term_days,
                'description' => $request->description,
                'created_by' => Auth::id()
            ]);

            // Update order with term
            $order = Order::findOrFail($request->order_id);
            $order->term_id = $term->id;
            $order->save();

            DB::commit();

            return redirect()->route('approval-order.index')
                           ->with('success', 'Term berhasil ditambahkan ke Order');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Gagal menambahkan term: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Display the specified approval.
     */
    public function show($id)
    {
        $order = Order::with(['pengirim', 'term', 'jenisBarang'])
                     ->findOrFail($id);

        return view('approval-order.show', compact('order'));
    }

    /**
     * Show the form for editing the specified approval.
     */
    public function edit($id)
    {
        $order = Order::with(['pengirim', 'jenisBarang', 'term'])->findOrFail($id);
        $terms = Term::orderBy('kode')->get();

        return view('approval-order.edit', compact('order', 'terms'));
    }

    /**
     * Update the specified approval.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'term_id' => 'required|exists:terms,id',
            'penerima' => 'nullable|string|max:255',
            'kontak_penerima' => 'nullable|string|max:255',
            'alamat_penerima' => 'nullable|string',
            'ftz03_option' => 'required|in:exclude,include,none',
            'sppb_option' => 'required|in:exclude,include,none',
            'buruh_bongkar_option' => 'required|in:exclude,include,none'
        ]);

        try {
            $order = Order::findOrFail($id);
            $order->term_id = $request->term_id;
            
            // Update Informasi Penerima
            $order->penerima = $request->penerima;
            $order->kontak_penerima = $request->kontak_penerima;
            $order->alamat_penerima = $request->alamat_penerima;
            
            // Update FTZ03
            $order->exclude_ftz03 = $request->ftz03_option == 'exclude';
            $order->include_ftz03 = $request->ftz03_option == 'include';
            
            // Update SPPB
            $order->exclude_sppb = $request->sppb_option == 'exclude';
            $order->include_sppb = $request->sppb_option == 'include';
            
            // Update Buruh Bongkar
            $order->exclude_buruh_bongkar = $request->buruh_bongkar_option == 'exclude';
            $order->include_buruh_bongkar = $request->buruh_bongkar_option == 'include';
            
            $order->save();

            return redirect()->route('approval-order.index')
                           ->with('success', 'Data Order berhasil diupdate');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal mengupdate data: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Remove the specified approval.
     */
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->term_id = null;
            $order->save();

            return redirect()->route('approval-order.index')
                           ->with('success', 'Term berhasil dihapus dari Order');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menghapus term: ' . $e->getMessage());
        }
    }

    /**
     * Approve the order.
     */
    public function approve($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->status = 'approved';
            $order->approved_by = Auth::id();
            $order->approved_at = Carbon::now();
            $order->save();

            return redirect()->route('approval-order.index')
                           ->with('success', 'Order berhasil disetujui');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menyetujui Order: ' . $e->getMessage());
        }
    }

    /**
     * Reject the order.
     */
    public function reject($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->status = 'rejected';
            $order->rejected_by = Auth::id();
            $order->rejected_at = Carbon::now();
            $order->save();

            return redirect()->route('approval-order.index')
                           ->with('success', 'Order berhasil ditolak');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menolak Order: ' . $e->getMessage());
        }
    }
}
