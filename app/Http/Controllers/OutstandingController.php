<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Term;
use App\Models\Pengirim;
use App\Models\JenisBarang;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OutstandingController extends Controller
{
    /**
     * Display a listing of outstanding orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['term', 'pengirim', 'jenisBarang', 'tujuanAmbil'])
                      ->outstanding()
                      ->approved() // Only show approved orders
                      ->latest();

        // Apply filters
        $this->applyFilters($query, $request);

        $orders = $query->paginate(15);

        // Get filter options
        $terms = Term::orderBy('nama_status')->get();
        $pengirims = Pengirim::orderBy('nama_pengirim')->get();

        return view('outstanding.index', compact('orders', 'terms', 'pengirims'));
    }

    /**
     * Display outstanding orders with specific status.
     */
    public function byStatus(Request $request, string $status)
    {
        $validStatuses = ['pending', 'partial', 'completed'];

        if (!in_array($status, $validStatuses)) {
            abort(404, 'Invalid status');
        }

        $query = Order::with(['term', 'pengirim', 'jenisBarang', 'tujuanAmbil'])
                      ->approved(); // Only show approved orders

        // Apply status filter
        switch ($status) {
            case 'pending':
                $query->pending();
                break;
            case 'partial':
                $query->partial();
                break;
            case 'completed':
                $query->completed();
                break;
        }

        // Apply additional filters
        $this->applyFilters($query, $request);

        $orders = $query->latest()->paginate(15);
        $stats = $this->getOutstandingStats();

        return view('outstanding.by_status', compact('orders', 'status', 'stats'));
    }

    /**
     * Process units for an order.
     */
    public function processUnits(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'processed_units' => 'required|integer|min:1|max:' . $order->sisa,
            'notes' => 'nullable|string|max:255'
        ]);

        try {
            $order->processUnits(
                $request->processed_units,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Units processed successfully',
                'order' => [
                    'id' => $order->id,
                    'sisa' => $order->sisa,
                    'completion_percentage' => $order->completion_percentage,
                    'outstanding_status' => $order->outstanding_status,
                    'status_badge' => $order->outstanding_status_badge,
                    'is_completed' => $order->isCompleted()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get outstanding statistics.
     */
    public function getStats(): JsonResponse
    {
        $stats = $this->getOutstandingStats();
        return response()->json($stats);
    }

    /**
     * Export outstanding orders to Excel.
     */
    public function export(Request $request)
    {
        $query = Order::outstanding()->with(['term', 'pengirim', 'jenisBarang', 'tujuanAmbil']);
        $this->applyFilters($query, $request);

        $orders = $query->get();

        // You can implement Excel export here
        return response()->json([
            'message' => 'Export functionality can be implemented here',
            'count' => $orders->count()
        ]);
    }

    /**
     * Apply filters to the query.
     */
    private function applyFilters($query, Request $request)
    {
        // Search filter
        if ($request->filled('search')) {
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

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_order', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_order', '<=', $request->date_to);
        }

        // Term filter
        if ($request->filled('term_id')) {
            $query->where('term_id', $request->term_id);
        }

        // Pengirim filter
        if ($request->filled('pengirim_id')) {
            $query->where('pengirim_id', $request->pengirim_id);
        }

        // Size filter
        if ($request->filled('size_kontainer')) {
            $query->where('size_kontainer', $request->size_kontainer);
        }

        // Outstanding status filter
        if ($request->filled('outstanding_status')) {
            $query->where('outstanding_status', $request->outstanding_status);
        }

        // Priority filter (orders with higher remaining units)
        if ($request->filled('priority') && $request->priority === 'high') {
            $query->where('sisa', '>', 5);
        }

        return $query;
    }

    /**
     * Get outstanding statistics.
     */
    private function getOutstandingStats(): array
    {
        return [
            'total_outstanding' => Order::outstanding()->approved()->count(),
            'pending' => Order::pending()->approved()->count(),
            'partial' => Order::partial()->approved()->count(),
            'completed' => Order::completed()->approved()->count(),
            'total_units_remaining' => Order::outstanding()->approved()->sum('sisa'),
            'total_units_ordered' => Order::outstanding()->approved()->sum('units'),
            'completion_rate' => $this->calculateCompletionRate(),
            'average_completion_time' => $this->getAverageCompletionTime()
        ];
    }

    /**
     * Calculate overall completion rate.
     */
    private function calculateCompletionRate(): float
    {
        $totalOrders = Order::count();
        $completedOrders = Order::completed()->count();

        return $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0;
    }

    /**
     * Get average completion time in days.
     */
    private function getAverageCompletionTime(): float
    {
        $completedOrders = Order::completed()
            ->whereNotNull('completed_at')
            ->selectRaw('AVG(DATEDIFF(completed_at, created_at)) as avg_days')
            ->first();

        return round($completedOrders->avg_days ?? 0, 1);
    }

    /**
     * Get filter options for dropdowns.
     */
    public function getFilterOptions(): JsonResponse
    {
        return response()->json([
            'terms' => Term::where('status', 'active')->select('id', 'nama_status')->get(),
            'pengirims' => Pengirim::where('status', 'active')->select('id', 'nama_pengirim')->get(),
            'sizes' => Order::outstanding()->distinct()->pluck('size_kontainer')->filter(),
            'outstanding_statuses' => ['pending', 'partial', 'completed']
        ]);
    }

    /**
     * Get order details for AJAX modal.
     */
    public function getOrderDetails(Order $order): JsonResponse
    {
        return response()->json([
            'id' => $order->id,
            'nomor_order' => $order->nomor_order,
            'units' => $order->units ?? 0,
            'sisa' => $order->sisa ?? 0,
            'completion_percentage' => $order->completion_percentage ?? 0,
            'outstanding_status' => $order->outstanding_status,
            'no_kontainer' => $order->no_kontainer,
            'term' => $order->term?->nama_status,
            'pengirim' => $order->pengirim?->nama_pengirim
        ]);
    }
}
