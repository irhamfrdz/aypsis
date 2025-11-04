<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderDataManagementController extends Controller
{
    /**
     * Display a listing of orders with incomplete data.
     */
    public function index(Request $request)
    {
        $query = Order::with(['term', 'pengirim', 'jenisBarang', 'tujuanAmbil'])
                      ->where(function($q) {
                          // Filter orders with incomplete data
                          $q->whereNull('pengirim_id')
                            ->orWhereNull('tujuan_ambil')
                            ->orWhereNull('tujuan_kirim') 
                            ->orWhereNull('tipe_kontainer')
                            ->orWhere('tujuan_ambil', '')
                            ->orWhere('tujuan_kirim', '')
                            ->orWhere('tipe_kontainer', '')
                            ->orWhereNull('no_tiket_do')
                            ->orWhere('no_tiket_do', '')
                            ->orWhereNull('status')
                            ->orWhere('status', '')
                            ->orWhereNull('term_id')
                            ->orWhereNull('jenis_barang_id')
                            ->orWhere(function($subQ) {
                                // For non-cargo containers, check size and unit
                                $subQ->where('tipe_kontainer', '!=', 'cargo')
                                     ->where(function($innerQ) {
                                         $innerQ->whereNull('size_kontainer')
                                                ->orWhereNull('unit_kontainer')
                                                ->orWhere('unit_kontainer', '<=', 0);
                                     });
                            })
                            ->orWhereNull('units')
                            ->orWhere('units', '');
                      })
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


}
