<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterDokumenKapalAlexindo;
use Carbon\Carbon;

class DokumenKapalAlexindoDashboardController extends Controller
{
    /**
     * Display the ship document expiry dashboard.
     */
    public function index()
    {
        $this->authorize('dashboard-dokumen-kapal-alexindo-view');
        $today = Carbon::today();
        $thirtyDaysLater = $today->copy()->addDays(30);
        
        // Semua dokumen berjangka (punya tanggal berakhir)
        $totalDokumens = MasterDokumenKapalAlexindo::with(['kapal', 'sertifikatKapal'])
            ->whereNotNull('tanggal_berakhir')
            ->orderBy('tanggal_berakhir', 'asc')
            ->get();

        // Asset yang akan jatuh tempo dalam 30 hari ke depan
        $expiringDokumens = MasterDokumenKapalAlexindo::with(['kapal', 'sertifikatKapal'])
            ->whereNotNull('tanggal_berakhir')
            ->whereDate('tanggal_berakhir', '>=', $today)
            ->whereDate('tanggal_berakhir', '<=', $thirtyDaysLater)
            ->orderBy('tanggal_berakhir', 'asc')
            ->get();
        
        // Asset yang sudah lewat jatuh tempo
        $expiredDokumens = MasterDokumenKapalAlexindo::with(['kapal', 'sertifikatKapal'])
            ->whereNotNull('tanggal_berakhir')
            ->whereDate('tanggal_berakhir', '<', $today)
            ->orderBy('tanggal_berakhir', 'desc')
            ->get();

        // Dokumen tanpa tanggal berakhir
        $noDateDokumens = MasterDokumenKapalAlexindo::with(['kapal', 'sertifikatKapal'])
            ->whereNull('tanggal_berakhir')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Statistics
        $stats = [
            'total_dokumen' => $totalDokumens->count(),
            'expiring_soon' => $expiringDokumens->count(),
            'expired' => $expiredDokumens->count(),
            'no_date' => $noDateDokumens->count(),
        ];
        
        return view('dashboards.dokumen-kapal-alexindo', compact('totalDokumens', 'expiringDokumens', 'expiredDokumens', 'noDateDokumens', 'stats'));
    }
}
