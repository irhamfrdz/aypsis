<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UangJalan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportUangJalanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        return view('report-uang-jalan.select-date');
    }

    public function view(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        if (!$request->has('start_date') || !$request->has('end_date')) {
            return redirect()->route('report.uang-jalan.index')
                ->with('error', 'Tanggal mulai dan tanggal akhir harus diisi');
        }

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $search = $request->input('search');

        $query = UangJalan::query()
            ->with(['suratJalan', 'suratJalanBongkaran', 'createdBy'])
            ->whereBetween('tanggal_uang_jalan', [$startDate, $endDate]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_uang_jalan', 'like', "%{$search}%")
                  ->orWhereHas('suratJalan', function($sq) use ($search) {
                      $sq->where('no_surat_jalan', 'like', "%{$search}%")
                         ->orWhere('supir', 'like', "%{$search}%")
                         ->orWhere('no_plat', 'like', "%{$search}%");
                  })
                  ->orWhereHas('suratJalanBongkaran', function($sq) use ($search) {
                      $sq->where('nomor_surat_jalan', 'like', "%{$search}%")
                         ->orWhere('supir', 'like', "%{$search}%")
                         ->orWhere('no_plat', 'like', "%{$search}%");
                  });
            });
        }

        $uangJalans = $query->orderBy('tanggal_uang_jalan', 'desc')->get();

        return view('report-uang-jalan.view', [
            'uangJalans' => $uangJalans,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'search' => $search
        ]);
    }
}
