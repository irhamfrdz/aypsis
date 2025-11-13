<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mobil;
use Carbon\Carbon;

class AssetDashboardController extends Controller
{
    /**
     * Display the asset insurance dashboard.
     */
    public function index()
    {
        // Get today's date
        $today = Carbon::today();
        
        // Get assets with insurance expiring within 30 days
        $expiringAssets = Mobil::whereNotNull('tanggal_jatuh_tempo_asuransi')
            ->whereDate('tanggal_jatuh_tempo_asuransi', '>=', $today)
            ->whereDate('tanggal_jatuh_tempo_asuransi', '<=', $today->copy()->addDays(30))
            ->orderBy('tanggal_jatuh_tempo_asuransi', 'asc')
            ->get();
        
        // Get assets with expired insurance
        $expiredAssets = Mobil::whereNotNull('tanggal_jatuh_tempo_asuransi')
            ->whereDate('tanggal_jatuh_tempo_asuransi', '<', $today)
            ->orderBy('tanggal_jatuh_tempo_asuransi', 'desc')
            ->get();
        
        // Get statistics
        $stats = [
            'total_assets' => Mobil::count(),
            'expiring_soon' => $expiringAssets->count(),
            'expired' => $expiredAssets->count(),
            'no_insurance_date' => Mobil::whereNull('tanggal_jatuh_tempo_asuransi')->count(),
        ];
        
        return view('dashboards.asset-insurance', compact('expiringAssets', 'expiredAssets', 'stats'));
    }
}
