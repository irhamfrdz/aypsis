<?php

namespace App\Services;

use App\Models\Coa;
use App\Models\CoaTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CoaTransactionService
{
    /**
     * Catat transaksi ke buku besar COA
     * 
     * @param string $namaAkun Nama akun COA
     * @param float $debit Jumlah debit
     * @param float $kredit Jumlah kredit
     * @param string $tanggalTransaksi Tanggal transaksi
     * @param string $nomorReferensi Nomor referensi (nomor pembayaran, invoice, dll)
     * @param string $jenisTransaksi Jenis transaksi (pembayaran, penerimaan, dll)
     * @param string|null $keterangan Keterangan transaksi
     * @return CoaTransaction|null
     */
    public function recordTransaction(
        string $namaAkun,
        float $debit,
        float $kredit,
        string $tanggalTransaksi,
        string $nomorReferensi,
        string $jenisTransaksi,
        ?string $keterangan = null
    ): ?CoaTransaction {
        $coa = Coa::where('nama_akun', $namaAkun)->first();
        
        if (!$coa) {
            \Log::warning("COA tidak ditemukan: {$namaAkun}");
            return null;
        }

        // Hitung saldo baru (saldo lama + debit - kredit)
        $saldoBaru = $coa->saldo + $debit - $kredit;

        // Buat record transaksi
        $transaction = CoaTransaction::create([
            'coa_id' => $coa->id,
            'tanggal_transaksi' => $tanggalTransaksi,
            'nomor_referensi' => $nomorReferensi,
            'jenis_transaksi' => $jenisTransaksi,
            'keterangan' => $keterangan,
            'debit' => $debit,
            'kredit' => $kredit,
            'saldo' => $saldoBaru,
            'created_by' => Auth::id()
        ]);

        // Update saldo di master COA
        $coa->saldo = $saldoBaru;
        $coa->save();

        return $transaction;
    }

    /**
     * Catat transaksi ganda (double entry)
     * 
     * @param array $debitAccount ['nama_akun' => string, 'jumlah' => float]
     * @param array $kreditAccount ['nama_akun' => string, 'jumlah' => float]
     * @param string $tanggalTransaksi
     * @param string $nomorReferensi
     * @param string $jenisTransaksi
     * @param string|null $keterangan
     * @return bool
     */
    public function recordDoubleEntry(
        array $debitAccount,
        array $kreditAccount,
        string $tanggalTransaksi,
        string $nomorReferensi,
        string $jenisTransaksi,
        ?string $keterangan = null
    ): bool {
        DB::beginTransaction();
        
        try {
            // Catat transaksi debit
            $this->recordTransaction(
                $debitAccount['nama_akun'],
                $debitAccount['jumlah'],
                0,
                $tanggalTransaksi,
                $nomorReferensi,
                $jenisTransaksi,
                $keterangan
            );

            // Catat transaksi kredit
            $this->recordTransaction(
                $kreditAccount['nama_akun'],
                0,
                $kreditAccount['jumlah'],
                $tanggalTransaksi,
                $nomorReferensi,
                $jenisTransaksi,
                $keterangan
            );

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error recording double entry: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Hapus transaksi dan kembalikan saldo
     * 
     * @param string $nomorReferensi
     * @return bool
     */
    public function deleteTransactionByReference(string $nomorReferensi): bool
    {
        DB::beginTransaction();
        
        try {
            $transactions = CoaTransaction::where('nomor_referensi', $nomorReferensi)->get();
            
            foreach ($transactions as $transaction) {
                $coa = $transaction->coa;
                
                // Kembalikan saldo (reverse transaksi)
                $coa->saldo = $coa->saldo - $transaction->debit + $transaction->kredit;
                $coa->save();
                
                // Hapus transaksi
                $transaction->delete();
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error deleting transaction: ' . $e->getMessage());
            return false;
        }
    }
}
