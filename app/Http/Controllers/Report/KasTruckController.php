<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Coa;
use App\Models\CoaTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KasTruckController extends Controller
{
    /**
     * Display the index reporting view for Kas Truck.
     */
    public function index(Request $request)
    {
        return view('report.kas-truck.select-date');
    }

    public function view(Request $request)
    {
        // Name of the account we are tracking (includes double space from database)
        $accountName = 'Bank BCA Trucking  - 168 2889 955';

        // Find the coa account by its exact name
        $akunCoa = Coa::where('nama_akun', $accountName)->first();

        $transactions = collect([]);
        $saldoAwal = 0;
        $saldoAkhir = 0;
        $totalDebit = 0;
        $totalKredit = 0;

        if ($akunCoa) {
            $query = CoaTransaction::with(['coa'])
                ->where('coa_id', $akunCoa->id);

            // Apply Date Filters
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            if ($startDate) {
                $query->whereDate('tanggal_transaksi', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('tanggal_transaksi', '<=', $endDate);
            }

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('keterangan', 'LIKE', '%'.$search.'%')
                        ->orWhere('nomor_referensi', 'LIKE', '%'.$search.'%');
                });
            }

            $transactions = $this->applyPalNetCashAmounts(
                $query->orderBy('tanggal_transaksi', 'asc')->orderBy('id', 'asc')->get(),
                $akunCoa->id
            );

            // Calculate initial balance (before start date) if start date is provided
            if ($startDate) {
                $pastTransactions = $this->applyPalNetCashAmounts(
                    CoaTransaction::where('coa_id', $akunCoa->id)
                        ->whereDate('tanggal_transaksi', '<', $startDate)
                        ->get(),
                    $akunCoa->id
                );

                foreach ($pastTransactions as $pt) {
                    $saldoAwal += $pt->debit;
                    $saldoAwal -= $pt->kredit;
                }
            } else {
                // if no date, original saldo should be from actual account?
                // actually if no start date, we just accumulate from the very first record.
            }

            // -------------------------------------------------------
            // Batch lookup nomor_accurate dari semua tabel transaksi
            // -------------------------------------------------------
            $referensiList = $transactions->pluck('nomor_referensi')->filter()->unique()->values()->toArray();
            $nomorAccurateMap = [];
            $akunPembayaranMap = [];
            $akunPembayaranIds = [];
            $rincianAkunStockMap = [];

            if (! empty($referensiList)) {
                // Tabel yang menggunakan kolom 'nomor' sebagai primary key referensi
                $tablesWithNomor = [
                    'pembayaran_aktivitas_lains',
                    'pembayaran_invoice_aktivitas_lain',
                ];
                foreach ($tablesWithNomor as $tbl) {
                    try {
                        $columns = ['nomor', 'nomor_accurate'];
                        foreach (['akun_coa_id', 'akun_bank_id', 'kas_bank_id', 'bank'] as $column) {
                            if (Schema::hasColumn($tbl, $column)) {
                                $columns[] = $column;
                            }
                        }
                        $rows = DB::table($tbl)->whereIn('nomor', $referensiList)->get($columns);
                        foreach ($rows as $row) {
                            $key = $row->nomor;
                            if (! empty($row->nomor_accurate) && ! isset($nomorAccurateMap[$key])) {
                                $nomorAccurateMap[$key] = $row->nomor_accurate;
                            }
                            if (! isset($akunPembayaranMap[$key])) {
                                $akunPembayaranMap[$key] = $row->akun_coa_id ?? $row->akun_bank_id ?? $row->kas_bank_id ?? $row->bank ?? null;
                                $akunId = $row->akun_coa_id ?? $row->akun_bank_id ?? $row->kas_bank_id ?? null;
                                if ($akunId) {
                                    $akunPembayaranIds[] = $akunId;
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        // Skip jika tabel/kolom tidak ada
                    }
                }

                // Tabel yang menggunakan kolom 'nomor_pembayaran' sebagai primary key referensi
                $tablesWithNomorPembayaran = [
                    'pembayaran_pranota_uang_jalans',
                    'pembayaran_pranota_uang_jalan_batams',
                    'pembayaran_pranota_uang_jalan_bongkarans',
                    'pembayaran_pranota_obs',
                    'pembayaran_obs',
                    'pembayaran_pranota_kontainer',
                    'pembayaran_pranota_vendor_supirs',
                    'pembayaran_dp_obs',
                    'pembayaran_biaya_kapals',
                    'pembayaran_pranota_lemburs',
                    'pembayaran_pranota_cat',
                    'pembayaran_pranota_rits',
                    'pembayaran_pranota_rit_keneks',
                    'pembayaran_pranota_stocks',
                    'pembatalan_surat_jalans',
                    'pembayaran_pranota_ongkos_truks',
                    'pembayaran_pranota_ob_antar_gudangs',
                ];
                foreach ($tablesWithNomorPembayaran as $tbl) {
                    try {
                        $columns = ['nomor_pembayaran', 'nomor_accurate'];
                        foreach (['akun_coa_id', 'akun_bank_id', 'kas_bank_id', 'bank'] as $column) {
                            if (Schema::hasColumn($tbl, $column)) {
                                $columns[] = $column;
                            }
                        }
                        $rows = DB::table($tbl)->whereIn('nomor_pembayaran', $referensiList)->get($columns);
                        foreach ($rows as $row) {
                            $key = $row->nomor_pembayaran;
                            if (! empty($row->nomor_accurate) && ! isset($nomorAccurateMap[$key])) {
                                $nomorAccurateMap[$key] = $row->nomor_accurate;
                            }
                            if (! isset($akunPembayaranMap[$key])) {
                                $akunPembayaranMap[$key] = $row->akun_coa_id ?? $row->akun_bank_id ?? $row->kas_bank_id ?? $row->bank ?? null;
                                $akunId = $row->akun_coa_id ?? $row->akun_bank_id ?? $row->kas_bank_id ?? null;
                                if ($akunId) {
                                    $akunPembayaranIds[] = $akunId;
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        // Skip jika tabel/kolom tidak ada
                    }
                }
            }

            // Fallback: data lama dimana nomor_referensi berupa ID integer
            // (bug lama di PembayaranObController yang mengirim $pembayaran->id bukan nomor_pembayaran)
            if (! empty($referensiList)) {
                $numericReferensiList = collect($referensiList)
                    ->filter(fn ($ref) => ctype_digit((string) $ref))
                    ->map(fn ($ref) => (int) $ref)
                    ->values()
                    ->toArray();

                if (! empty($numericReferensiList)) {
                    try {
                        $rows = DB::table('pembayaran_obs')
                            ->whereIn('id', $numericReferensiList)
                            ->select('id', 'nomor_accurate', 'akun_coa_id')
                            ->get();
                        foreach ($rows as $row) {
                            $idKey = (string) $row->id;
                            if (! empty($row->nomor_accurate) && ! isset($nomorAccurateMap[$idKey])) {
                                $nomorAccurateMap[$idKey] = $row->nomor_accurate;
                            }
                            if (! isset($akunPembayaranMap[$idKey]) && $row->akun_coa_id) {
                                $akunPembayaranMap[$idKey] = $row->akun_coa_id;
                                $akunPembayaranIds[] = $row->akun_coa_id;
                            }
                        }
                    } catch (\Exception $e) {
                        // Skip
                    }
                }
            }
            // -------------------------------------------------------

            if (! empty($referensiList) && Schema::hasTable('pembayaran_pranota_stock_akun_items')) {
                $rincianAkunStock = DB::table('pembayaran_pranota_stock_akun_items as rincian')
                    ->join('pembayaran_pranota_stocks as pembayaran', 'pembayaran.id', '=', 'rincian.pembayaran_pranota_stock_id')
                    ->join('akun_coa as akun', 'akun.id', '=', 'rincian.akun_coa_id')
                    ->whereIn('pembayaran.nomor_pembayaran', $referensiList)
                    ->whereNull('pembayaran.deleted_at')
                    ->orderBy('rincian.id')
                    ->get(['pembayaran.nomor_pembayaran', 'akun.nama_akun', 'rincian.nominal']);

                foreach ($rincianAkunStock as $rincian) {
                    $rincianAkunStockMap[$rincian->nomor_pembayaran][] = [
                        'nama_akun' => $rincian->nama_akun,
                        'nominal' => $rincian->nominal,
                    ];
                }
            }

            // -------------------------------------------------------
            // Attach nomor_accurate dan Sortir
            // -------------------------------------------------------
            $namaAkunMap = Coa::whereIn('id', array_unique($akunPembayaranIds))
                ->pluck('nama_akun', 'id');

            foreach ($transactions as $t) {
                $t->nomor_accurate = $nomorAccurateMap[$t->nomor_referensi] ?? null;
                $akun = $akunPembayaranMap[$t->nomor_referensi] ?? null;
                $t->akun_pembayaran = $namaAkunMap[$akun] ?? $akun;
                $t->rincian_akun_pembayaran = $t->jenis_transaksi === 'Pembayaran Pranota Stock'
                    ? ($rincianAkunStockMap[$t->nomor_referensi] ?? [])
                    : [];
            }

            // Urutkan: TOP UP SALDO AWAL paling atas, sisanya by nomor_accurate
            $transactions = $transactions->sortBy(function ($t) {
                // Prioritas 1: Top Up Saldo Awal (selalu paling atas)
                if (str_contains(strtoupper($t->keterangan ?? ''), 'TOP UP SALDO AWAL')) {
                    return '0000000000';
                }

                $key = $t->nomor_accurate ?? $t->nomor_referensi ?? '';

                // Pastikan null/kosong muncul di paling bawah (setelah transaksi yang punya nomor)
                return $key === '' ? 'ZZZZZZZZZZ' : $key;
            })->values();

            // -------------------------------------------------------
            // Hitung Running Balances (berdasarkan urutan yang sudah disortir)
            // -------------------------------------------------------
            $runningBalance = $saldoAwal;

            foreach ($transactions as $t) {
                $runningBalance += $t->debit;
                $runningBalance -= $t->kredit;

                $totalDebit += $t->debit;
                $totalKredit += $t->kredit;

                // Add virtual attribute for view rendering
                $t->running_balance = $runningBalance;
            }

            $saldoAkhir = $runningBalance;
        }

        return view('report.kas-truck.index', compact(
            'akunCoa',
            'accountName',
            'transactions',
            'saldoAwal',
            'saldoAkhir',
            'totalDebit',
            'totalKredit'
        ));
    }

    private function applyPalNetCashAmounts($transactions, int $bankAccountId)
    {
        $references = $transactions->pluck('nomor_referensi')->filter()->unique();
        if ($references->isEmpty()) {
            return $transactions;
        }

        $netAmounts = DB::table('pembayaran_aktivitas_lains')
            ->whereIn('nomor', $references)
            ->where('akun_bank_id', $bankAccountId)
            ->where('pph_nominal', '>', 0)
            ->whereNull('deleted_at')
            ->pluck('grand_total', 'nomor');

        foreach ($transactions as $transaction) {
            $netAmount = $netAmounts[$transaction->nomor_referensi] ?? null;
            if ($netAmount === null || $transaction->jenis_transaksi !== 'Pembayaran Aktivitas Lain') {
                continue;
            }

            if ($transaction->debit > 0) {
                $transaction->debit = $netAmount;
            } elseif ($transaction->kredit > 0) {
                $transaction->kredit = $netAmount;
            }
        }

        return $transactions;
    }

    public function topup(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string',
        ]);

        $accountName = 'Bank BCA Trucking  - 168 2889 955';
        $akunCoa = Coa::where('nama_akun', $accountName)->first();

        if (! $akunCoa) {
            return redirect()->back()->with('error', 'Gagal, Akun COA master tidak ditemukan di sistem.');
        }

        CoaTransaction::create([
            'coa_id' => $akunCoa->id,
            'tanggal_transaksi' => $request->tanggal,
            'nomor_referensi' => $request->no_referensi ?? '-',
            'jenis_transaksi' => 'Debit', // Memasukkan dana
            'keterangan' => $request->keterangan,
            'debit' => $request->nominal,
            'kredit' => 0,
            'created_by' => auth()->id() ?? 1,
        ]);

        return redirect()->back()->with('success', 'Top-Up Saldo berhasil direkam ke riwayat Kas Trucking!');
    }

    public function swap($id)
    {
        $trx = CoaTransaction::findOrFail($id);

        // Swap values
        $tempDebit = $trx->debit;
        $trx->debit = $trx->kredit;
        $trx->kredit = $tempDebit;

        // Update jenis_transaksi if needed
        if ($trx->debit > 0) {
            $trx->jenis_transaksi = 'Debit';
        } elseif ($trx->kredit > 0) {
            $trx->jenis_transaksi = 'Kredit';
        }

        $trx->save();

        return redirect()->back()->with('success', 'Berhasil menukar posisi Pemasukan/Pengeluaran!');
    }

    public function updateStartingBalance(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'saldo_awal' => 'required|numeric',
        ]);

        $accountName = 'Bank BCA Trucking  - 168 2889 955';
        $akunCoa = Coa::where('nama_akun', $accountName)->first();

        if (! $akunCoa) {
            return redirect()->back()->with('error', 'Gagal, Akun COA master tidak ditemukan di sistem.');
        }

        $startDate = $request->input('start_date');
        $newSaldoAwal = $request->input('saldo_awal');

        // Calculate current initial balance (before start date)
        $currentSaldoAwal = 0;
        $pastTransactions = CoaTransaction::where('coa_id', $akunCoa->id)
            ->whereDate('tanggal_transaksi', '<', $startDate)
            ->get();

        foreach ($pastTransactions as $pt) {
            $currentSaldoAwal += $pt->debit;
            $currentSaldoAwal -= $pt->kredit;
        }

        $difference = $newSaldoAwal - $currentSaldoAwal;
        $adjustmentDate = \Carbon\Carbon::parse($startDate)->subDay()->format('Y-m-d');

        // Find existing adjustment transaction on the day before start date
        $adjustmentTrx = CoaTransaction::where('coa_id', $akunCoa->id)
            ->whereDate('tanggal_transaksi', $adjustmentDate)
            ->where('keterangan', 'Penyesuaian Saldo Sebelum Periode')
            ->first();

        if ($adjustmentTrx) {
            $netTrx = $adjustmentTrx->debit - $adjustmentTrx->kredit;
            $newNet = $netTrx + $difference;

            if ($newNet == 0) {
                $adjustmentTrx->delete();
            } else {
                $adjustmentTrx->update([
                    'debit' => $newNet > 0 ? $newNet : 0,
                    'kredit' => $newNet < 0 ? abs($newNet) : 0,
                    'jenis_transaksi' => $newNet > 0 ? 'Debit' : 'Kredit',
                ]);
            }
        } else {
            if ($difference != 0) {
                CoaTransaction::create([
                    'coa_id' => $akunCoa->id,
                    'tanggal_transaksi' => $adjustmentDate,
                    'nomor_referensi' => '-',
                    'jenis_transaksi' => $difference > 0 ? 'Debit' : 'Kredit',
                    'keterangan' => 'Penyesuaian Saldo Sebelum Periode',
                    'debit' => $difference > 0 ? $difference : 0,
                    'kredit' => $difference < 0 ? abs($difference) : 0,
                    'created_by' => auth()->id() ?? 1,
                ]);
            }
        }

        return redirect()->route('report.kas-truck.view', [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'search' => $request->input('search'),
        ])->with('success', 'Saldo Sebelum Periode (Mulai) berhasil disesuaikan!');
    }
}
