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

        // Rekap Mutasi Uang Jalan (Debit) untuk semua supir
        $debitAllDrivers = DB::table('uang_jalans')
            ->join('surat_jalans', 'uang_jalans.surat_jalan_id', '=', 'surat_jalans.id')
            ->whereNotIn('surat_jalans.status', ['cancelled', 'draft'])
            ->where('surat_jalans.status_pembayaran_uang_jalan', 'dibayar')
            ->whereNull('uang_jalans.deleted_at')
            ->select(
                'surat_jalans.supir',
                DB::raw("SUM(CASE WHEN DATE(uang_jalans.tanggal_uang_jalan) <= '{$hariIni->format('Y-m-d')}' THEN uang_jalans.jumlah_total ELSE 0 END) as total_debit"),
                DB::raw("SUM(CASE WHEN DATE(uang_jalans.tanggal_uang_jalan) < '{$hariIni->format('Y-m-d')}' THEN uang_jalans.jumlah_total ELSE 0 END) as debit_sebelumnya")
            )
            ->groupBy('surat_jalans.supir')
            ->get()
            ->keyBy('supir');

        // Rekap Mutasi Uang Jalan (Kredit) untuk semua supir
        $kreditAllDrivers = DB::table('uang_jalans')
            ->join('surat_jalans', 'uang_jalans.surat_jalan_id', '=', 'surat_jalans.id')
            ->whereNotIn('surat_jalans.status', ['cancelled', 'draft'])
            ->where('surat_jalans.status_pembayaran_uang_jalan', 'dibayar')
            ->whereNull('uang_jalans.deleted_at')
            ->select(
                'surat_jalans.supir',
                DB::raw("SUM(CASE WHEN DATE(uang_jalans.tanggal_uang_jalan) <= '{$hariIni->format('Y-m-d')}' AND EXISTS (SELECT 1 FROM tanda_terimas WHERE tanda_terimas.surat_jalan_id = surat_jalans.id AND DATE(tanda_terimas.created_at) <= '{$hariIni->format('Y-m-d')}') THEN uang_jalans.jumlah_total ELSE 0 END) as total_kredit"),
                DB::raw("SUM(CASE WHEN DATE(uang_jalans.tanggal_uang_jalan) < '{$hariIni->format('Y-m-d')}' AND EXISTS (SELECT 1 FROM tanda_terimas WHERE tanda_terimas.surat_jalan_id = surat_jalans.id AND DATE(tanda_terimas.created_at) < '{$hariIni->format('Y-m-d')}') THEN uang_jalans.jumlah_total ELSE 0 END) as kredit_sebelumnya")
            )
            ->groupBy('surat_jalans.supir')
            ->get()
            ->keyBy('supir');

        $rekapSupirBelumTandaTerima = collect();

        $getMutasi = function($nama) use ($debitAllDrivers, $kreditAllDrivers) {
            $d = $debitAllDrivers->get($nama);
            $k = $kreditAllDrivers->get($nama);
            $debitSeb = $d ? $d->debit_sebelumnya : 0;
            $kreditSeb = $k ? $k->kredit_sebelumnya : 0;
            $totalDebit = $d ? $d->total_debit : 0;
            $totalKredit = $k ? $k->total_kredit : 0;
            return (object) [
                'saldo_awal' => $debitSeb - $kreditSeb,
                'debit' => $totalDebit,
                'kredit' => $totalKredit,
                'saldo_akhir' => $totalDebit - $totalKredit
            ];
        };

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
                'mutasi' => $getMutasi($nama)
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
                    'is_vendor' => false,
                    'mutasi' => $getMutasi($pending->supir)
                ]);
            }
        }

        // 3. Masukkan grup Supir Non-AYP (Customer & Vendor) sebagai satu card
        if (!empty($supirNonAypNames)) {
            $terakhirSjNonAyp = \App\Models\SuratJalan::whereIn('supir', $supirNonAypNames)
                ->whereNotIn('status', ['cancelled', 'draft'])
                ->whereDate('tanggal_surat_jalan', '<=', $hariIni)
                ->max('tanggal_surat_jalan');

            $debitSebNonAyp = 0;
            $kreditSebNonAyp = 0;
            $totalDebitNonAyp = 0;
            $totalKreditNonAyp = 0;

            foreach ($supirNonAypNames as $nama) {
                $d = $debitAllDrivers->get($nama);
                $k = $kreditAllDrivers->get($nama);
                $debitSebNonAyp += $d ? $d->debit_sebelumnya : 0;
                $kreditSebNonAyp += $k ? $k->kredit_sebelumnya : 0;
                $totalDebitNonAyp += $d ? $d->total_debit : 0;
                $totalKreditNonAyp += $k ? $k->total_kredit : 0;
            }

            $rekapSupirBelumTandaTerima->push((object) [
                'supir' => 'NON_AYP', // Used for filtering
                'nama_lengkap' => 'SUPIR NON-AYP',
                'total' => $totalPendingNonAyp,
                'oldest_uang_jalan' => $oldestUjNonAyp,
                'terakhir_surat_jalan' => $terakhirSjNonAyp,
                'is_jakarta' => false,
                'is_customer' => false,
                'is_vendor' => false,
                'is_non_ayp_group' => true,
                'mutasi' => (object) [
                    'saldo_awal' => $debitSebNonAyp - $kreditSebNonAyp,
                    'debit' => $totalDebitNonAyp,
                    'kredit' => $totalKreditNonAyp,
                    'saldo_akhir' => $totalDebitNonAyp - $totalKreditNonAyp
                ]
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

            // Total uang jalan yang diterima supir (DEBIT = uang keluar dari perusahaan ke supir)
            $totalDebit = DB::table('uang_jalans')
                ->join('surat_jalans', 'uang_jalans.surat_jalan_id', '=', 'surat_jalans.id')
                ->whereIn('surat_jalans.supir', $filterSupirNames)
                ->whereNotIn('surat_jalans.status', ['cancelled', 'draft'])
                ->where('surat_jalans.status_pembayaran_uang_jalan', 'dibayar')
                ->whereDate('uang_jalans.tanggal_uang_jalan', '<=', $hariIni)
                ->whereNull('uang_jalans.deleted_at')
                ->sum('uang_jalans.jumlah_total');

            // Total kredit (sudah Tanda Terima) akumulasi
            $totalKredit = DB::table('uang_jalans')
                ->join('surat_jalans', 'uang_jalans.surat_jalan_id', '=', 'surat_jalans.id')
                ->whereIn('surat_jalans.supir', $filterSupirNames)
                ->whereNotIn('surat_jalans.status', ['cancelled', 'draft'])
                ->where('surat_jalans.status_pembayaran_uang_jalan', 'dibayar')
                ->whereDate('uang_jalans.tanggal_uang_jalan', '<=', $hariIni)
                ->whereExists(function ($query) use ($hariIni) {
                    $query->select(DB::raw(1))
                          ->from('tanda_terimas')
                          ->whereColumn('tanda_terimas.surat_jalan_id', 'surat_jalans.id')
                          ->whereDate('tanda_terimas.created_at', '<=', $hariIni);
                })
                ->whereNull('uang_jalans.deleted_at')
                ->sum('uang_jalans.jumlah_total');

            // Total debit sebelum hari ini (kemarin)
            $debitSebelumnya = DB::table('uang_jalans')
                ->join('surat_jalans', 'uang_jalans.surat_jalan_id', '=', 'surat_jalans.id')
                ->whereIn('surat_jalans.supir', $filterSupirNames)
                ->whereNotIn('surat_jalans.status', ['cancelled', 'draft'])
                ->where('surat_jalans.status_pembayaran_uang_jalan', 'dibayar')
                ->whereDate('uang_jalans.tanggal_uang_jalan', '<', $hariIni)
                ->whereNull('uang_jalans.deleted_at')
                ->sum('uang_jalans.jumlah_total');

            // Total kredit sebelum hari ini (kemarin)
            $kreditSebelumnya = DB::table('uang_jalans')
                ->join('surat_jalans', 'uang_jalans.surat_jalan_id', '=', 'surat_jalans.id')
                ->whereIn('surat_jalans.supir', $filterSupirNames)
                ->whereNotIn('surat_jalans.status', ['cancelled', 'draft'])
                ->where('surat_jalans.status_pembayaran_uang_jalan', 'dibayar')
                ->whereDate('uang_jalans.tanggal_uang_jalan', '<', $hariIni)
                ->whereExists(function ($query) use ($hariIni) {
                    $query->select(DB::raw(1))
                          ->from('tanda_terimas')
                          ->whereColumn('tanda_terimas.surat_jalan_id', 'surat_jalans.id')
                          ->whereDate('tanda_terimas.created_at', '<', $hariIni);
                })
                ->whereNull('uang_jalans.deleted_at')
                ->sum('uang_jalans.jumlah_total');

            $mutasiUangJalan = (object)[
                'supir' => $selectedSupir,
                'total_debit' => $totalDebit,
                'total_kredit' => $totalKredit,
                'saldo_awal' => $debitSebelumnya - $kreditSebelumnya,
                'saldo_akhir' => $totalDebit - $totalKredit,
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
