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

            // Mulai dari saldo 0 untuk periode ini agar laporan bersih dari data testing lama
            $saldoAwal = 0;
            
            // Calculate running balances
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
