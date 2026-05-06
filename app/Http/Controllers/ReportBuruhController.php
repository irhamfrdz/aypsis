<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BiayaKapalTenagaKerja;
use Carbon\Carbon;

class ReportBuruhController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $query = BiayaKapalTenagaKerja::with(['buruh', 'biayaKapal'])
            ->whereHas('biayaKapal', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            });

        if ($request->filled('buruh_id')) {
            $query->where('buruh_id', $request->buruh_id);
        }

        $reports = $query->orderBy('biaya_kapal_id', 'desc')->get();
        
        $allBuruhs = \App\Models\Buruh::orderBy('nama')->get();

        return view('reports.buruh.index', compact('reports', 'startDate', 'endDate', 'allBuruhs'));
    }
}
