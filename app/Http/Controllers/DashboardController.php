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

        // Parameter Tanggal Filter
        $filterDate = request('tanggal_dashboard', Carbon::today()->format('Y-m-d'));
        $hariIni = Carbon::parse($filterDate)->startOfDay();

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
        $oneMonthLater = $hariIni->copy()->addMonth();

        // Asset yang asuransinya sudah lewat (expired)
        $assetsExpired = Mobil::whereNotNull('tanggal_jatuh_tempo_asuransi')
            ->whereDate('tanggal_jatuh_tempo_asuransi', '<', $hariIni)
            ->orderBy('tanggal_jatuh_tempo_asuransi', 'asc')
            ->get();

        // Asset yang asuransinya akan jatuh tempo dalam 1 bulan
        $assetsExpiringSoon = Mobil::whereNotNull('tanggal_jatuh_tempo_asuransi')
            ->whereDate('tanggal_jatuh_tempo_asuransi', '>=', $hariIni)
            ->whereDate('tanggal_jatuh_tempo_asuransi', '<=', $oneMonthLater)
            ->orderBy('tanggal_jatuh_tempo_asuransi', 'asc')
            ->get();

        // Ambil daftar nama supir Non-AYP (Customer + Vendor)
        $supirNonAypNames = DB::table('surat_jalans')
            ->leftJoin('tagihan_supir_vendors', 'surat_jalans.id', '=', 'tagihan_supir_vendors.surat_jalan_id')
            ->where(function($q) {
                $q->where('surat_jalans.is_supir_customer', true)
                  ->orWhereNotNull('tagihan_supir_vendors.id');
            })
            ->whereNotIn('surat_jalans.status', ['cancelled', 'draft'])
            ->whereNotNull('surat_jalans.supir')
            ->where('surat_jalans.supir', '!=', '')
            ->whereDate('surat_jalans.tanggal_surat_jalan', '<=', $hariIni)
            ->distinct()
            ->pluck('surat_jalans.supir')
            ->toArray();

        // Data Surat Jalan yang belum ada tanda terimanya (hanya yang sudah bayar uang jalan)
        $perPage = request('per_page', 10);
        $suratJalanBelumTandaTerima = \App\Models\SuratJalan::with(['pengirimRelation', 'tujuanPengirimanRelation', 'uangJalan'])
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->where('status_pembayaran_uang_jalan', 'dibayar')
            ->whereDate('tanggal_surat_jalan', '<=', $hariIni)
            ->where(function($q) use ($hariIni) {
                $q->doesntHave('tandaTerima')
                  ->orWhereHas('tandaTerima', function($q2) use ($hariIni) {
                      $q2->whereDate('created_at', '>', $hariIni);
                  });
            })
            ->when(request('supir'), function ($q) use ($supirNonAypNames) {
                if (request('supir') === 'NON_AYP') {
                    return $q->whereIn('supir', $supirNonAypNames);
                }
                return $q->where('supir', request('supir'));
            })
            ->orderBy('tanggal_surat_jalan', 'desc')
            ->paginate($perPage)
            ->appends(request()->all());

        // Rekap jumlah surat jalan per supir yang belum ada tanda terima
        $pendingTandaTerima = \App\Models\SuratJalan::leftJoin('uang_jalans', 'surat_jalans.id', '=', 'uang_jalans.surat_jalan_id')
            ->whereNotIn('surat_jalans.status', ['cancelled', 'draft'])
            ->where('surat_jalans.status_pembayaran_uang_jalan', 'dibayar')
            ->whereDate('surat_jalans.tanggal_surat_jalan', '<=', $hariIni)
            ->where(function($q) use ($hariIni) {
                $q->doesntHave('tandaTerima')
                  ->orWhereHas('tandaTerima', function($q2) use ($hariIni) {
                      $q2->whereDate('created_at', '>', $hariIni);
                  });
            })
            ->select('surat_jalans.supir', DB::raw('count(surat_jalans.id) as total'), DB::raw('MIN(uang_jalans.tanggal_uang_jalan) as oldest_uang_jalan'))
            ->groupBy('surat_jalans.supir')
            ->get();

        // Ambil semua supir Jakarta untuk melihat siapa yang tidak kerja
        $supirJakarta = DB::table('karyawans')
            ->where('karyawans.divisi', 'LIKE', '%SUPIR%')
            ->whereIn('karyawans.pekerjaan', ['SUPIR TRUCK', 'SUPIR TRAILER'])
            ->where('karyawans.cabang', 'LIKE', '%JAKARTA%')
            ->where('karyawans.status', 'active')
            ->whereNull('karyawans.tanggal_berhenti')
            ->where('karyawans.nama_panggilan', '!=', 'IBP')
            ->leftJoin('surat_jalans', function ($join) use ($hariIni) {
                $join->on('karyawans.nama_panggilan', '=', 'surat_jalans.supir')
                    ->whereNotIn('surat_jalans.status', ['cancelled', 'draft'])
                    ->whereDate('surat_jalans.tanggal_surat_jalan', '<=', $hariIni);
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
            // Jangan masukkan jika dia termasuk supir Non-AYP
            if (in_array($nama, $supirNonAypNames)) continue;
            
            $pending = $pendingTandaTerima->firstWhere('supir', $nama);
            $rekapSupirBelumTandaTerima->push((object) [
                'supir' => $nama,
                'nama_lengkap' => $data->nama_lengkap,
                'total' => $pending ? $pending->total : 0,
                'oldest_uang_jalan' => $pending ? $pending->oldest_uang_jalan : null,
                'terakhir_surat_jalan' => $data->terakhir_surat_jalan,
                'is_jakarta' => true,
            ]);
        }

        // 2. Masukkan supir lain (non-Jakarta) yang punya pending tanda terima
        $totalPendingNonAyp = 0;
        $oldestUjNonAyp = null;

        foreach ($pendingTandaTerima as $pending) {
            if (in_array($pending->supir, $supirNonAypNames)) {
                $totalPendingNonAyp += $pending->total;
                if (!$oldestUjNonAyp || $pending->oldest_uang_jalan < $oldestUjNonAyp) {
                    $oldestUjNonAyp = $pending->oldest_uang_jalan;
                }
                continue;
            }

            if (! isset($supirJakarta[$pending->supir])) {
                $lastSj = \App\Models\SuratJalan::where('supir', $pending->supir)
                    ->whereNotIn('status', ['cancelled', 'draft'])
                    ->whereDate('tanggal_surat_jalan', '<=', $hariIni)
                    ->max('tanggal_surat_jalan');

                $rekapSupirBelumTandaTerima->push((object) [
                    'supir' => $pending->supir,
                    'nama_lengkap' => $pending->supir,
                    'total' => $pending->total,
                    'oldest_uang_jalan' => $pending->oldest_uang_jalan,
                    'terakhir_surat_jalan' => $lastSj,
                    'is_jakarta' => false,
                    'is_customer' => false,
                    'is_vendor' => false
                ]);
            }
        }

        // 3. Masukkan grup Supir Non-AYP (Customer & Vendor) sebagai satu card
        if (!empty($supirNonAypNames)) {
            $terakhirSjNonAyp = \App\Models\SuratJalan::whereIn('supir', $supirNonAypNames)
                ->whereNotIn('status', ['cancelled', 'draft'])
                ->whereDate('tanggal_surat_jalan', '<=', $hariIni)
                ->max('tanggal_surat_jalan');

            $rekapSupirBelumTandaTerima->push((object) [
                'supir' => 'NON_AYP', // Used for filtering
                'nama_lengkap' => 'SUPIR NON-AYP',
                'total' => $totalPendingNonAyp,
                'oldest_uang_jalan' => $oldestUjNonAyp,
                'terakhir_surat_jalan' => $terakhirSjNonAyp,
                'is_jakarta' => false,
                'is_customer' => false,
                'is_vendor' => false,
                'is_non_ayp_group' => true
            ]);
        }

        // Sorting: Yang punya pending SJ paling banyak di atas, lalu yang paling lama nganggur
        $rekapSupirBelumTandaTerima = $rekapSupirBelumTandaTerima->sortByDesc(function ($item) use ($hariIni) {
            // Prioritas 1: Jumlah pending (desc)
            // Prioritas 2: Jika 0, maka urutkan berdasarkan paling lama tidak SJ
            $score = $item->total * 100000;
            if ($item->total == 0) {
                if (! $item->terakhir_surat_jalan) {
                    $score = 99999; // Belum pernah dapat SJ, taruh di atas yang nol
                } else {
                    $days = \Carbon\Carbon::parse($item->terakhir_surat_jalan)->startOfDay()->diffInDays($hariIni);
                    $score = $days; // Semakin lama tidak SJ, semakin atas
                }
            }

            return $score;
        })->values();

        // Mutasi Uang Jalan per supir (jika supir dipilih)
        $mutasiUangJalan = null;
        if (request('supir')) {
            $selectedSupir = request('supir');
            
            // Tentukan nama-nama supir yang difilter
            $filterSupirNames = [];
            if ($selectedSupir === 'NON_AYP') {
                $filterSupirNames = $supirNonAypNames;
            } else {
                $filterSupirNames = [$selectedSupir];
            }

            // 1. SALDO AWAL (Outstanding uang jalan dari hari-hari sebelum tanggal filter)
            $awalDebit = DB::table('uang_jalans')
                ->join('surat_jalans', 'uang_jalans.surat_jalan_id', '=', 'surat_jalans.id')
                ->whereIn('surat_jalans.supir', $filterSupirNames)
                ->whereNotIn('surat_jalans.status', ['cancelled', 'draft'])
                ->whereDate('uang_jalans.tanggal_uang_jalan', '<', $hariIni)
                ->whereNull('uang_jalans.deleted_at')
                ->sum('uang_jalans.jumlah_total');

            $awalKredit = DB::table('uang_jalans')
                ->join('surat_jalans', 'uang_jalans.surat_jalan_id', '=', 'surat_jalans.id')
                ->join('tanda_terimas', 'surat_jalans.id', '=', 'tanda_terimas.surat_jalan_id')
                ->whereIn('surat_jalans.supir', $filterSupirNames)
                ->whereNotIn('surat_jalans.status', ['cancelled', 'draft'])
                ->whereDate('uang_jalans.tanggal_uang_jalan', '<', $hariIni)
                ->whereDate('tanda_terimas.created_at', '<', $hariIni)
                ->whereNull('uang_jalans.deleted_at')
                ->sum('uang_jalans.jumlah_total');

            $saldoAwal = $awalDebit - $awalKredit;

            // 2. DEBIT (Uang jalan yang diberikan tepat pada tanggal filter)
            $debitHariIni = DB::table('uang_jalans')
                ->join('surat_jalans', 'uang_jalans.surat_jalan_id', '=', 'surat_jalans.id')
                ->whereIn('surat_jalans.supir', $filterSupirNames)
                ->whereNotIn('surat_jalans.status', ['cancelled', 'draft'])
                ->whereDate('uang_jalans.tanggal_uang_jalan', '=', $hariIni)
                ->whereNull('uang_jalans.deleted_at')
                ->sum('uang_jalans.jumlah_total');

            // 3. KREDIT (Tanda terima yang diserahkan/diselesaikan tepat pada tanggal filter)
            $kreditHariIni = DB::table('uang_jalans')
                ->join('surat_jalans', 'uang_jalans.surat_jalan_id', '=', 'surat_jalans.id')
                ->join('tanda_terimas', 'surat_jalans.id', '=', 'tanda_terimas.surat_jalan_id')
                ->whereIn('surat_jalans.supir', $filterSupirNames)
                ->whereNotIn('surat_jalans.status', ['cancelled', 'draft'])
                ->whereDate('uang_jalans.tanggal_uang_jalan', '<=', $hariIni)
                ->whereDate('tanda_terimas.created_at', '=', $hariIni)
                ->whereNull('uang_jalans.deleted_at')
                ->sum('uang_jalans.jumlah_total');

            // 4. SALDO AKHIR
            $saldoAkhir = $saldoAwal + $debitHariIni - $kreditHariIni;

            $mutasiUangJalan = (object)[
                'supir' => $selectedSupir,
                'total_debit' => $debitHariIni,
                'total_kredit' => $kreditHariIni,
                'saldo_awal' => $saldoAwal, 
                'saldo_akhir' => $saldoAkhir,
            ];
        }

        // Mengirim semua data ke view 'dashboard'
        return view('dashboard', compact('prospekData', 'assetsExpired', 'assetsExpiringSoon', 'suratJalanBelumTandaTerima', 'rekapSupirBelumTandaTerima', 'filterDate', 'mutasiUangJalan'));
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
