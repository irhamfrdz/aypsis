<?php

namespace App\Http\Controllers;

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\TagihanCat;
use Illuminate\Http\Request;

class ReportTagihanController extends Controller
{
    public function index(Request $request)
    {
        // Filter parameters - default to last 3 months for better data visibility
        $startDate = $request->input('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $jenisTagihan = $request->input('jenis_tagihan', 'all'); // all, sewa, cat
        $status = $request->input('status', 'all'); // all, unpaid, paid, approved

        // Initialize query collections
        $tagihanSewa = collect();
        $tagihanCat = collect();

        // Get Tagihan Sewa Kontainer
        if ($jenisTagihan === 'all' || $jenisTagihan === 'sewa') {
            $query = DaftarTagihanKontainerSewa::query()
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal_awal', [$startDate, $endDate])
                        ->orWhereBetween('tanggal_akhir', [$startDate, $endDate])
                        ->orWhereBetween('created_at', [$startDate, $endDate]);
                });

            if ($status !== 'all') {
                $query->where('status', $status);
            }

            $tagihanSewa = $query->orderBy('tanggal_awal', 'desc')->get();
        }

        // Get Tagihan CAT
        if ($jenisTagihan === 'all' || $jenisTagihan === 'cat') {
            $query = TagihanCat::query()
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal_cat', [$startDate, $endDate])
                        ->orWhereBetween('created_at', [$startDate, $endDate]);
                });

            if ($status !== 'all') {
                $query->where('status', $status);
            }

            $tagihanCat = $query->orderBy('tanggal_cat', 'desc')->get();
        }

        // Calculate summary
        $totalTagihan = $tagihanSewa->count() + $tagihanCat->count();
        $totalNilai = $tagihanSewa->sum('total_tagihan') +
                      $tagihanCat->sum('biaya_cat');

        $totalPaid = $tagihanSewa->where('status', 'paid')->count() +
                     $tagihanCat->where('status', 'paid')->count();

        $totalUnpaid = $tagihanSewa->where('status', 'unpaid')->count() +
                       $tagihanCat->where('status', 'unpaid')->count();

        return view('report.tagihan.index', compact(
            'tagihanSewa',
            'tagihanCat',
            'startDate',
            'endDate',
            'jenisTagihan',
            'status',
            'totalTagihan',
            'totalNilai',
            'totalPaid',
            'totalUnpaid'
        ));
    }

    public function export(Request $request)
    {
        // Export functionality (Excel/PDF) - to be implemented
        return response()->json(['message' => 'Export feature coming soon']);
    }
}
