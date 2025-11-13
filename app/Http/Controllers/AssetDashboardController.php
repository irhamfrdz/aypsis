<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mobil;
use Carbon\Carbon;

class AssetDashboardController extends Controller
{
    /**
     * Display the asset expiry dashboard for multiple types (Asuransi, Plat, KIR, Pajak).
     */
    public function index()
    {
        $today = Carbon::today();
        $thirtyDaysLater = $today->copy()->addDays(30);
        
        // ASURANSI - tanggal_jatuh_tempo_asuransi
        $asuransiExpiring = Mobil::whereNotNull('tanggal_jatuh_tempo_asuransi')
            ->whereDate('tanggal_jatuh_tempo_asuransi', '>=', $today)
            ->whereDate('tanggal_jatuh_tempo_asuransi', '<=', $thirtyDaysLater)
            ->orderBy('tanggal_jatuh_tempo_asuransi', 'asc')
            ->get();
        
        $asuransiExpired = Mobil::whereNotNull('tanggal_jatuh_tempo_asuransi')
            ->whereDate('tanggal_jatuh_tempo_asuransi', '<', $today)
            ->orderBy('tanggal_jatuh_tempo_asuransi', 'desc')
            ->get();
        
        // PLAT/STNK - pajak_stnk
        $platExpiring = Mobil::whereNotNull('pajak_stnk')
            ->whereDate('pajak_stnk', '>=', $today)
            ->whereDate('pajak_stnk', '<=', $thirtyDaysLater)
            ->orderBy('pajak_stnk', 'asc')
            ->get();
        
        $platExpired = Mobil::whereNotNull('pajak_stnk')
            ->whereDate('pajak_stnk', '<', $today)
            ->orderBy('pajak_stnk', 'desc')
            ->get();
        
        // KIR - pajak_kir
        $kirExpiring = Mobil::whereNotNull('pajak_kir')
            ->whereDate('pajak_kir', '>=', $today)
            ->whereDate('pajak_kir', '<=', $thirtyDaysLater)
            ->orderBy('pajak_kir', 'asc')
            ->get();
        
        $kirExpired = Mobil::whereNotNull('pajak_kir')
            ->whereDate('pajak_kir', '<', $today)
            ->orderBy('pajak_kir', 'desc')
            ->get();
        
        // PAJAK - pajak_plat
        $pajakExpiring = Mobil::whereNotNull('pajak_plat')
            ->whereDate('pajak_plat', '>=', $today)
            ->whereDate('pajak_plat', '<=', $thirtyDaysLater)
            ->orderBy('pajak_plat', 'asc')
            ->get();
        
        $pajakExpired = Mobil::whereNotNull('pajak_plat')
            ->whereDate('pajak_plat', '<', $today)
            ->orderBy('pajak_plat', 'desc')
            ->get();
        
        // Statistics for each type
        $stats = [
            'asuransi' => [
                'total_assets' => Mobil::whereNotNull('tanggal_jatuh_tempo_asuransi')->count(),
                'expiring_soon' => $asuransiExpiring->count(),
                'expired' => $asuransiExpired->count(),
                'no_date' => Mobil::whereNull('tanggal_jatuh_tempo_asuransi')->count(),
            ],
            'plat' => [
                'total_assets' => Mobil::whereNotNull('pajak_stnk')->count(),
                'expiring_soon' => $platExpiring->count(),
                'expired' => $platExpired->count(),
                'no_date' => Mobil::whereNull('pajak_stnk')->count(),
            ],
            'kir' => [
                'total_assets' => Mobil::whereNotNull('pajak_kir')->count(),
                'expiring_soon' => $kirExpiring->count(),
                'expired' => $kirExpired->count(),
                'no_date' => Mobil::whereNull('pajak_kir')->count(),
            ],
            'pajak' => [
                'total_assets' => Mobil::whereNotNull('pajak_plat')->count(),
                'expiring_soon' => $pajakExpiring->count(),
                'expired' => $pajakExpired->count(),
                'no_date' => Mobil::whereNull('pajak_plat')->count(),
            ],
        ];
        
        // Collections for each type
        $expiringAssets = [
            'asuransi' => $asuransiExpiring,
            'plat' => $platExpiring,
            'kir' => $kirExpiring,
            'pajak' => $pajakExpiring,
        ];
        
        $expiredAssets = [
            'asuransi' => $asuransiExpired,
            'plat' => $platExpired,
            'kir' => $kirExpired,
            'pajak' => $pajakExpired,
        ];
        
        return view('dashboards.asset-expiry', compact('expiringAssets', 'expiredAssets', 'stats'));
    }
}
