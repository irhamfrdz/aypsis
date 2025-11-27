<?php

namespace App\Http\Controllers;

use App\Models\UangJalanBongkaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PranotaUangJalanBongkaranController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Permission check - reuse existing permission key
        if (!$user->can('pranota-uang-jalan-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pranota uang jalan bongkaran.');
        }

        $query = UangJalanBongkaran::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_uang_jalan', 'like', "%{$search}%")
                  ->orWhere('nomor_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%");
            });
        }

        // Filter by status if provided (mapping from UI 'status' values)
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'unpaid') {
                $query->where('status', 'belum_dibayar');
            } elseif ($status === 'paid') {
                $query->where('status', 'lunas');
            } elseif ($status === 'cancelled') {
                $query->where('status', 'dibatalkan');
            }
        }

        $stats = [
            'total' => UangJalanBongkaran::count(),
            'this_month' => UangJalanBongkaran::whereMonth('created_at', now()->month)->count(),
            'unpaid' => UangJalanBongkaran::where('status', 'belum_dibayar')->count(),
            'paid' => UangJalanBongkaran::where('status', 'lunas')->count(),
        ];

        $perPage = 20;
        $rows = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->query());

        return view('pranota-uang-jalan-bongkaran.index', compact('rows', 'stats'));
    }
}
