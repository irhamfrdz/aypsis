<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderApprovedNotification;
use App\Notifications\OrderRejectedNotification;
use Illuminate\Http\Request;

class OrderApprovalController extends Controller
{
    /**
     * Display a listing of pending approval orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['term', 'pengirim', 'jenisBarang', 'tujuanAmbil', 'approvedBy'])
                      ->pendingApproval()
                      ->latest();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_order', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('tujuan_kirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_ambil', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15);

        return view('orders.approval.index', compact('orders'));
    }

    /**
     * Approve an order.
     */
    public function approve(Request $request, Order $order)
    {
        if (!$order->isPendingApproval()) {
            return back()->with('error', 'Order sudah diproses sebelumnya.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $order->approve(auth()->id(), $request->notes);

        // Send notification to admins and order creator
        $this->sendApprovalNotification($order, auth()->user(), $request->notes);

        return back()->with('success', 'Order berhasil disetujui!');
    }

    /**
     * Reject an order.
     */
    public function reject(Request $request, Order $order)
    {
        if (!$order->isPendingApproval()) {
            return back()->with('error', 'Order sudah diproses sebelumnya.');
        }

        $request->validate([
            'notes' => 'required|string|max:500'
        ]);

        $order->reject(auth()->id(), $request->notes);

        // Send notification to admins and order creator
        $this->sendRejectionNotification($order, auth()->user(), $request->notes);

        return back()->with('success', 'Order berhasil ditolak.');
    }

    /**
     * Bulk approve orders.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'notes' => 'nullable|string|max:500'
        ]);

        $orders = Order::whereIn('id', $request->order_ids)
                      ->pendingApproval()
                      ->get();

        foreach ($orders as $order) {
            $order->approve(auth()->id(), $request->notes);
            
            // Send notification for each order
            $this->sendApprovalNotification($order, auth()->user(), $request->notes);
        }

        return back()->with('success', count($orders) . ' order berhasil disetujui!');
    }

    /**
     * Send approval notification to relevant users.
     */
    protected function sendApprovalNotification($order, $approver, $notes)
    {
        // Get all admin users
        $admins = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->get();

        // Send notification to all admins
        foreach ($admins as $admin) {
            if ($admin->id !== $approver->id) {
                $admin->notify(new OrderApprovedNotification($order, $approver, $notes));
            }
        }
    }

    /**
     * Send rejection notification to relevant users.
     */
    protected function sendRejectionNotification($order, $rejector, $notes)
    {
        // Get all admin users
        $admins = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->get();

        // Send notification to all admins
        foreach ($admins as $admin) {
            if ($admin->id !== $rejector->id) {
                $admin->notify(new OrderRejectedNotification($order, $rejector, $notes));
            }
        }
    }
}
