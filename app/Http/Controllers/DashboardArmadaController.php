<?php

namespace App\Http\Controllers;

use App\Models\ArmadaUnavailability;
use App\Models\Mobil;
use App\Services\ArmadaRankingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardArmadaController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tanggal' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'periode' => ['nullable', 'integer', 'in:7,30'],
            'search' => ['nullable', 'string', 'max:255'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'jenis' => ['nullable', 'string', 'max:255'],
            'mode' => ['nullable', 'in:tabel,diagram'],
        ]);
        $tanggalRanking = $request->filled('tanggal') ? Carbon::parse($request->tanggal) : today()->subDay();
        $periode = $request->filled('periode') ? $request->integer('periode') : 7;
        $rankingMode = $request->input('mode') ?: 'tabel';
        $tanggalMulai = $tanggalRanking->copy()->subDays($periode - 1);
        $query = Mobil::whereNotNull('imei_gps')->whereRaw("TRIM(imei_gps) <> ''");
        if ($request->user()?->karyawan?->cabang === 'BTM') {
            $query->where('lokasi', 'BTM');
        }

        $lokasis = (clone $query)->whereNotNull('lokasi')->whereRaw("TRIM(lokasi) <> ''")
            ->distinct()->orderBy('lokasi')->pluck('lokasi');
        $jenises = (clone $query)->whereNotNull('jenis')->whereRaw("TRIM(jenis) <> ''")
            ->distinct()->orderBy('jenis')->pluck('jenis');
        $fleetOptions = (clone $query)->orderBy('nomor_polisi')->get(['id', 'nomor_polisi']);
        $search = trim((string) $request->input('search', ''));
        $lokasi = (string) $request->input('lokasi', '');
        $jenis = (string) $request->input('jenis', '');

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('nomor_polisi', 'like', '%'.$search.'%')
                    ->orWhere('kode_no', 'like', '%'.$search.'%')
                    ->orWhere('nickname', 'like', '%'.$search.'%')
                    ->orWhere('pemakai', 'like', '%'.$search.'%')
                    ->orWhereHas('karyawan', fn ($query) => $query
                        ->where('nama_lengkap', 'like', '%'.$search.'%')
                        ->orWhere('nik', 'like', '%'.$search.'%'));
            });
        }
        if ($lokasi !== '') {
            $query->where('lokasi', $lokasi);
        }
        if ($jenis !== '') {
            $query->where('jenis', $jenis);
        }

        $totalArmada = (clone $query)->count();
        $perLokasi = (clone $query)
            ->selectRaw("COALESCE(NULLIF(TRIM(lokasi), ''), 'Tanpa Lokasi') AS nama_lokasi, COUNT(*) AS total")
            ->groupByRaw("COALESCE(NULLIF(TRIM(lokasi), ''), 'Tanpa Lokasi')")
            ->orderByDesc('total')->orderBy('nama_lokasi')->get();
        $rankingRows = app(ArmadaRankingService::class)->build(
            (clone $query)->with('karyawan:id,nama_lengkap,nik')
                ->get(['id', 'nomor_polisi', 'nickname', 'lokasi', 'jenis', 'karyawan_id', 'pemakai']), $tanggalRanking, $periode
        );
        $rankingSummary = [
            'rit' => $rankingRows->sum('rit'),
            'gps_km' => $rankingRows->sum('gps_km'),
            'available_days' => $rankingRows->sum('available_days'),
            'productive_days' => $rankingRows->where('plate_valid', true)->sum('productive_days'),
            'unavailable_days' => $rankingRows->sum('unavailable_days'),
            'moving_without_rit_days' => $rankingRows->where('plate_valid', true)->sum('moving_without_rit_days'),
            'idle_days' => $rankingRows->where('plate_valid', true)->sum('idle_days'),
            'unknown_days' => $rankingRows->sum('unknown_days'),
            'incomplete_vehicles' => $rankingRows->filter(fn ($row) => $row['unranked_reason'] !== null || $row['gps_missing_days'] > 0)->count(),
            'ranked_vehicles' => $rankingRows->whereNotNull('rank')->count(),
            'provisional_vehicles' => $rankingRows->where('rank_is_provisional', true)->count(),
        ];
        $rankingPage = max(1, $request->integer('ranking_page', 1));
        $rankings = new LengthAwarePaginator($rankingRows->forPage($rankingPage, 15)->values(), $rankingRows->count(), 15, $rankingPage, [
            'path' => $request->url(), 'pageName' => 'ranking_page', 'fragment' => 'ranking-armada',
        ]);
        $rankings->appends($request->except('ranking_page'));
        $armadas = $query->with('karyawan')->orderBy('nomor_polisi')->orderBy('id')
            ->paginate(15)->withQueryString();
        $availabilityPeriods = ArmadaUnavailability::with('mobil')->whereIn('mobil_id', $fleetOptions->pluck('id'))
            ->whereDate('start_date', '<=', $tanggalRanking->toDateString())
            ->where(fn ($query) => $query->whereNull('end_date')->orWhereDate('end_date', '>=', $tanggalMulai->toDateString()))
            ->orderByDesc('start_date')->orderByDesc('id')->paginate(5, ['*'], 'availability_page')->withQueryString();

        return view('dashboard-armada.index', compact(
            'armadas', 'search', 'lokasi', 'jenis', 'lokasis', 'jenises',
            'totalArmada', 'perLokasi', 'tanggalRanking', 'tanggalMulai', 'periode', 'rankings', 'rankingSummary',
            'fleetOptions', 'availabilityPeriods', 'rankingMode'
        ));
    }
}
