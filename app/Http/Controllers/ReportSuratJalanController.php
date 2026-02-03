<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\ReportSuratJalanExport; // Nanti dibuat

class ReportSuratJalanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        return view('report-surat-jalan.select-date');
    }

    public function view(Request $request)
    {
        // Placeholder untuk logic menampilkan report
        $user = Auth::user();

        if (!$user->can('surat-jalan-view')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        return "Fitur Report Surat Jalan sedang dalam pengembangan. Tanggal yang dipilih: " . $request->start_date . " s/d " . $request->end_date;
        // Logic sebenarnya nanti akan fetching data lalu return view('report-surat-jalan.view', ...)
    }
}
