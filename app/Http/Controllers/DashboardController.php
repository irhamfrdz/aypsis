<?php

namespace App\Http\Controllers;

use App\Models\Kontainer;
use App\Models\Mobil;
use App\Models\Prospek;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    /**
     * Menampilkan halaman dashboard dengan data ringkasan.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $user = Auth::user();

        // Check if user is a driver (supir) - redirect to supir dashboard
        if ($user->isSupir()) {
            return redirect()->route('supir.dashboard');
        }

        // Check if user has any meaningful permissions (exclude basic auth permissions)
        $meaningfulPermissions = $user->permissions
            ->whereNotIn('name', ['login', 'logout']) // Exclude basic auth permissions
            ->count();

        // If user has no meaningful permissions, show special dashboard
        if ($meaningfulPermissions == 0) {
            return view('dashboard_no_permissions');
        }

        // Only check dashboard permission if user has meaningful permissions
        if (! $user->can('dashboard')) {
            return view('welcome');
        }

        // Data prospek berdasarkan kombinasi tujuan dan ukuran kontainer
        $prospekData = [
            'Jakarta' => [
                '20ft' => $this->getProspekByTujuanUkuran('Jakarta', '20'),
                '40ft' => $this->getProspekByTujuanUkuran('Jakarta', '40'),
                'Cargo' => $this->getProspekByTujuanTipe('Jakarta', 'CARGO'),
            ],
            'Batam' => [
                '20ft' => $this->getProspekByTujuanUkuran('Batam', '20'),
                '40ft' => $this->getProspekByTujuanUkuran('Batam', '40'),
                'Cargo' => $this->getProspekByTujuanTipe('Batam', 'CARGO'),
            ],
            'Pinang' => [
                '20ft' => $this->getProspekByTujuanUkuran('Pinang', '20'),
                '40ft' => $this->getProspekByTujuanUkuran('Pinang', '40'),
                'Cargo' => $this->getProspekByTujuanTipe('Pinang', 'CARGO'),
            ],
        ];

        // Data Asset Asuransi
        $today = Carbon::today();
        $oneMonthLater = Carbon::today()->addMonth();

        // Asset yang asuransinya sudah lewat (expired)
        $assetsExpired = Mobil::whereNotNull('tanggal_jatuh_tempo_asuransi')
            ->whereDate('tanggal_jatuh_tempo_asuransi', '<', $today)
            ->orderBy('tanggal_jatuh_tempo_asuransi', 'asc')
            ->get();

        // Asset yang asuransinya akan jatuh tempo dalam 1 bulan
        $assetsExpiringSoon = Mobil::whereNotNull('tanggal_jatuh_tempo_asuransi')
            ->whereDate('tanggal_jatuh_tempo_asuransi', '>=', $today)
            ->whereDate('tanggal_jatuh_tempo_asuransi', '<=', $oneMonthLater)
            ->orderBy('tanggal_jatuh_tempo_asuransi', 'asc')
            ->get();

        // Data Surat Jalan yang belum ada tanda terimanya (hanya yang sudah bayar uang jalan)
        $perPage = request('per_page', 10);
        $suratJalanBelumTandaTerima = \App\Models\SuratJalan::doesntHave('tandaTerima')
            ->with(['pengirimRelation', 'tujuanPengirimanRelation', 'uangJalan'])
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->where('status_pembayaran_uang_jalan', 'dibayar')
            ->when(request('supir'), function ($q) {
                return $q->where('supir', request('supir'));
            })
            ->orderBy('tanggal_surat_jalan', 'desc')
            ->paginate($perPage)
            ->appends(request()->all());

        // Rekap jumlah surat jalan per supir yang belum ada tanda terima
        $pendingTandaTerima = \App\Models\SuratJalan::doesntHave('tandaTerima')
            ->leftJoin('uang_jalans', 'surat_jalans.id', '=', 'uang_jalans.surat_jalan_id')
            ->whereNotIn('surat_jalans.status', ['cancelled', 'draft'])
            ->where('surat_jalans.status_pembayaran_uang_jalan', 'dibayar')
            ->select('surat_jalans.supir', DB::raw('count(*) as total'), DB::raw('MIN(uang_jalans.tanggal_uang_jalan) as oldest_uang_jalan'))
            ->groupBy('surat_jalans.supir')
            ->get();

        // Ambil semua supir Jakarta untuk melihat siapa yang tidak kerja
        $supirJakarta = DB::table('karyawans')
            ->where('karyawans.divisi', 'LIKE', '%SUPIR%')
            ->where('karyawans.cabang', 'LIKE', '%JAKARTA%')
            ->where('karyawans.status', 'active')
            ->leftJoin('surat_jalans', function($join) {
                $join->on('karyawans.nama_panggilan', '=', 'surat_jalans.supir')
                     ->whereNotIn('surat_jalans.status', ['cancelled', 'draft']);
            })
            ->select(
                'karyawans.nama_panggilan',
                'karyawans.nama_lengkap',
                DB::raw('MAX(surat_jalans.tanggal_surat_jalan) as terakhir_surat_jalan')
            )
            ->groupBy('karyawans.id', 'karyawans.nama_panggilan', 'karyawans.nama_lengkap')
            ->get()
            ->keyBy('nama_panggilan');

        $rekapSupirBelumTandaTerima = collect();

        // 1. Masukkan semua supir Jakarta
        foreach ($supirJakarta as $nama => $data) {
            $pending = $pendingTandaTerima->firstWhere('supir', $nama);
            $rekapSupirBelumTandaTerima->push((object)[
                'supir' => $nama,
                'nama_lengkap' => $data->nama_lengkap,
                'total' => $pending ? $pending->total : 0,
                'oldest_uang_jalan' => $pending ? $pending->oldest_uang_jalan : null,
                'terakhir_surat_jalan' => $data->terakhir_surat_jalan,
                'is_jakarta' => true
            ]);
        }

        // 2. Masukkan supir lain (non-Jakarta) yang punya pending tanda terima
        foreach ($pendingTandaTerima as $pending) {
            if (!isset($supirJakarta[$pending->supir])) {
                $lastSj = \App\Models\SuratJalan::where('supir', $pending->supir)
                    ->whereNotIn('status', ['cancelled', 'draft'])
                    ->max('tanggal_surat_jalan');
                    
                $rekapSupirBelumTandaTerima->push((object)[
                    'supir' => $pending->supir,
                    'nama_lengkap' => $pending->supir,
                    'total' => $pending->total,
                    'oldest_uang_jalan' => $pending->oldest_uang_jalan,
                    'terakhir_surat_jalan' => $lastSj,
                    'is_jakarta' => false
                ]);
            }
        }

        // Sorting: Yang punya pending SJ paling banyak di atas, lalu yang paling lama nganggur
        $rekapSupirBelumTandaTerima = $rekapSupirBelumTandaTerima->sortByDesc(function ($item) {
            // Prioritas 1: Jumlah pending (desc)
            // Prioritas 2: Jika 0, maka urutkan berdasarkan paling lama tidak SJ
            $score = $item->total * 100000;
            if ($item->total == 0) {
                if (!$item->terakhir_surat_jalan) {
                    $score = 99999; // Belum pernah dapat SJ, taruh di atas yang nol
                } else {
                    $days = \Carbon\Carbon::parse($item->terakhir_surat_jalan)->startOfDay()->diffInDays(now()->startOfDay());
                    $score = $days; // Semakin lama tidak SJ, semakin atas
                }
            }
            return $score;
        })->values();

        // Mengirim semua data ke view 'dashboard'
        return view('dashboard', compact('prospekData', 'assetsExpired', 'assetsExpiringSoon', 'suratJalanBelumTandaTerima', 'rekapSupirBelumTandaTerima'));
    }

    /**
     * Menghitung jumlah prospek berdasarkan tujuan dan ukuran kontainer
     */
    private function getProspekByTujuanUkuran($tujuan, $ukuran)
    {
        return Prospek::where('tujuan_pengiriman', 'like', "%{$tujuan}%")
            ->where('ukuran', $ukuran)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '')
                    ->orWhere('status', 'aktif');
            })
            ->count();
    }

    /**
     * Menghitung jumlah prospek berdasarkan tujuan dan tipe (untuk cargo)
     */
    private function getProspekByTujuanTipe($tujuan, $tipe)
    {
        return Prospek::where('tujuan_pengiriman', 'like', "%{$tujuan}%")
            ->where('tipe', $tipe)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '')
                    ->orWhere('status', 'aktif');
            })
            ->count();
    }
}
