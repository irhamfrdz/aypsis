<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Coa;
use App\Models\CoaTransaction;

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
                $query->where(function($q) use ($search) {
                    $q->where('keterangan', 'LIKE', '%' . $search . '%')
                      ->orWhere('nomor_referensi', 'LIKE', '%' . $search . '%');
                });
            }

            $transactions = $query->orderBy('tanggal_transaksi', 'asc')->orderBy('id', 'asc')->get();

            // Calculate initial balance (before start date) if start date is provided
            if ($startDate) {
                $pastTransactions = CoaTransaction::where('coa_id', $akunCoa->id)
                    ->whereDate('tanggal_transaksi', '<', $startDate)
                    ->get();
                    
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

            if (!empty($referensiList)) {
                // Tabel yang menggunakan kolom 'nomor' sebagai primary key referensi
                $tablesWithNomor = [
                    'pembayaran_aktivitas_lains',
                    'pembayaran_invoice_aktivitas_lain',
                ];
                foreach ($tablesWithNomor as $tbl) {
                    try {
                        $rows = DB::table($tbl)
                            ->whereIn('nomor', $referensiList)
                            ->whereNotNull('nomor_accurate')
                            ->where('nomor_accurate', '!=', '')
                            ->pluck('nomor_accurate', 'nomor');
                        foreach ($rows as $key => $accurate) {
                            if (!isset($nomorAccurateMap[$key])) {
                                $nomorAccurateMap[$key] = $accurate;
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
                    'pembayaran_pranota_obs',
                    'pembayaran_obs',
                    'pembayaran_pranota_kontainer',
                    'pembayaran_pranota_vendor_supirs',
                    'pembayaran_dp_obs',
                    'pembayaran_biaya_kapals',
                ];
                foreach ($tablesWithNomorPembayaran as $tbl) {
                    try {
                        $rows = DB::table($tbl)
                            ->whereIn('nomor_pembayaran', $referensiList)
                            ->whereNotNull('nomor_accurate')
                            ->where('nomor_accurate', '!=', '')
                            ->pluck('nomor_accurate', 'nomor_pembayaran');
                        foreach ($rows as $key => $accurate) {
                            if (!isset($nomorAccurateMap[$key])) {
                                $nomorAccurateMap[$key] = $accurate;
                            }
                        }
                    } catch (\Exception $e) {
                        // Skip jika tabel/kolom tidak ada
                    }
                }
            }

            // Fallback: data lama dimana nomor_referensi berupa ID integer
            // (bug lama di PembayaranObController yang mengirim $pembayaran->id bukan nomor_pembayaran)
            if (!empty($referensiList)) {
                $numericReferensiList = collect($referensiList)
                    ->filter(fn($ref) => ctype_digit((string)$ref))
                    ->map(fn($ref) => (int)$ref)
                    ->values()
                    ->toArray();

                if (!empty($numericReferensiList)) {
                    try {
                        $rows = DB::table('pembayaran_obs')
                            ->whereIn('id', $numericReferensiList)
                            ->whereNotNull('nomor_accurate')
                            ->where('nomor_accurate', '!=', '')
                            ->select('id', 'nomor_accurate')
                            ->get();
                        foreach ($rows as $row) {
                            $idKey = (string) $row->id;
                            if (!isset($nomorAccurateMap[$idKey])) {
                                $nomorAccurateMap[$idKey] = $row->nomor_accurate;
                            }
                        }
                    } catch (\Exception $e) {
                        // Skip
                    }
                }
            }
            // -------------------------------------------------------

            // -------------------------------------------------------
            // Attach nomor_accurate dan Sortir
            // -------------------------------------------------------
            foreach ($transactions as $t) {
                $t->nomor_accurate = $nomorAccurateMap[$t->nomor_referensi] ?? null;
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

    public function topup(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string',
        ]);

        $accountName = 'Bank BCA Trucking  - 168 2889 955';
        $akunCoa = Coa::where('nama_akun', $accountName)->first();

        if (!$akunCoa) {
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
}
