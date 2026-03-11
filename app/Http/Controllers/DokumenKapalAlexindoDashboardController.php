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
        $today = Carbon::today();
        $thirtyDaysLater = $today->copy()->addDays(30);
        
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
        
        // Statistics
        $stats = [
            'total_dokumen' => MasterDokumenKapalAlexindo::whereNotNull('tanggal_berakhir')->count(),
            'expiring_soon' => $expiringDokumens->count(),
            'expired' => $expiredDokumens->count(),
            'no_date' => MasterDokumenKapalAlexindo::whereNull('tanggal_berakhir')->count(),
        ];
        
        return view('dashboards.dokumen-kapal-alexindo', compact('expiringDokumens', 'expiredDokumens', 'stats'));
    }
}
