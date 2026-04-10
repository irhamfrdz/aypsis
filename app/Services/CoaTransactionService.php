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

    /**
     * Catat pembayaran biaya kapal
     *
     * @param mixed $pembayaran
     * @return bool
     */
    public function pembayaranBiayaKapal($pembayaran): bool
    {
        DB::beginTransaction();
        try {
            foreach ($pembayaran->biayaKapals as $biaya) {
                $nominal = $biaya->pivot->nominal ?? $biaya->total_biaya ?? $biaya->nominal;
                
                // Determine expense account based on classification
                $namaAkunBiaya = 'Biaya Kapal Lain-Lain'; // Fallback
                $klasifikasi = $biaya->klasifikasiBiaya->nama ?? '';
                
                if (stripos($klasifikasi, 'perbaikan') !== false) $namaAkunBiaya = 'Biaya Perbaikan Kapal';
                elseif (stripos($klasifikasi, 'perlengkapan') !== false) $namaAkunBiaya = 'Biaya Perlengkapan Kapal';
                elseif (stripos($klasifikasi, 'perijinan') !== false) $namaAkunBiaya = 'Biaya Sertifikat dan Perijinan Kapal';
                elseif (stripos($klasifikasi, 'bunker') !== false || stripos($klasifikasi, 'bbm') !== false) $namaAkunBiaya = 'Biaya Bunker Kapal';
                elseif (stripos($klasifikasi, 'pelumas') !== false) $namaAkunBiaya = 'Biaya Pelumas Kapal';
                
                if ($pembayaran->jenis_transaksi == 'debit') {
                    // DEBIT bank (increase), KREDIT expense (decrease) - typically for refunds/corrections
                    $this->recordDoubleEntry(
                        ['nama_akun' => $pembayaran->bank, 'jumlah' => $nominal],
                        ['nama_akun' => $namaAkunBiaya, 'jumlah' => $nominal],
                        $pembayaran->tanggal_pembayaran,
                        $pembayaran->nomor_pembayaran,
                        'pembayaran_biaya_kapal',
                        "Penerimaan (Refund/Koreksi) {$biaya->nomor_invoice} - {$biaya->nama_kapal} ({$klasifikasi})"
                    );
                } else {
                    // DEBIT expense (increase), KREDIT bank (decrease) - normal payment
                    $this->recordDoubleEntry(
                        ['nama_akun' => $namaAkunBiaya, 'jumlah' => $nominal],
                        ['nama_akun' => $pembayaran->bank, 'jumlah' => $nominal],
                        $pembayaran->tanggal_pembayaran,
                        $pembayaran->nomor_pembayaran,
                        'pembayaran_biaya_kapal',
                        "Pembayaran {$biaya->nomor_invoice} - {$biaya->nama_kapal} ({$klasifikasi})"
                    );
                }
            }

            // Record adjustment (penyesuaian) if exists
            if ($pembayaran->total_tagihan_penyesuaian != 0) {
                $penyesuaian = $pembayaran->total_tagihan_penyesuaian;
                $isDebitAdjustment = ($penyesuaian > 0);
                $absPenyesuaian = abs($penyesuaian);
                
                // Adjustment account (usually mapping to Biaya Kapal Lain-Lain or similar)
                $namaAkunPenyesuaian = 'Biaya Kapal Lain-Lain';
                
                if ($pembayaran->jenis_transaksi == 'debit') {
                    // Logic for debit transaction adjustments
                    if ($isDebitAdjustment) {
                        // Increase bank, decrease expense
                        $this->recordDoubleEntry(
                            ['nama_akun' => $pembayaran->bank, 'jumlah' => $absPenyesuaian],
                            ['nama_akun' => $namaAkunPenyesuaian, 'jumlah' => $absPenyesuaian],
                            $pembayaran->tanggal_pembayaran,
                            $pembayaran->nomor_pembayaran,
                            'pembayaran_biaya_kapal',
                            "Penyesuaian (+) Pembayaran Biaya Kapal: " . ($pembayaran->alasan_penyesuaian ?? 'Tanpa alasan')
                        );
                    } else {
                        // Decrease bank, increase expense (negative adjustment in a refund context)
                        $this->recordDoubleEntry(
                            ['nama_akun' => $namaAkunPenyesuaian, 'jumlah' => $absPenyesuaian],
                            ['nama_akun' => $pembayaran->bank, 'jumlah' => $absPenyesuaian],
                            $pembayaran->tanggal_pembayaran,
                            $pembayaran->nomor_pembayaran,
                            'pembayaran_biaya_kapal',
                            "Penyesuaian (-) Pembayaran Biaya Kapal: " . ($pembayaran->alasan_penyesuaian ?? 'Tanpa alasan')
                        );
                    }
                } else {
                    // Logic for credit (normal payment) transaction adjustments
                    if ($isDebitAdjustment) {
                        // Increase expense, decrease bank (positive adjustment: extra cost)
                        $this->recordDoubleEntry(
                            ['nama_akun' => $namaAkunPenyesuaian, 'jumlah' => $absPenyesuaian],
                            ['nama_akun' => $pembayaran->bank, 'jumlah' => $absPenyesuaian],
                            $pembayaran->tanggal_pembayaran,
                            $pembayaran->nomor_pembayaran,
                            'pembayaran_biaya_kapal',
                            "Penyesuaian (+) Pembayaran Biaya Kapal: " . ($pembayaran->alasan_penyesuaian ?? 'Tanpa alasan')
                        );
                    } else {
                        // Decrease expense, increase bank (negative adjustment: discount/reduction)
                        $this->recordDoubleEntry(
                            ['nama_akun' => $pembayaran->bank, 'jumlah' => $absPenyesuaian],
                            ['nama_akun' => $namaAkunPenyesuaian, 'jumlah' => $absPenyesuaian],
                            $pembayaran->tanggal_pembayaran,
                            $pembayaran->nomor_pembayaran,
                            'pembayaran_biaya_kapal',
                            "Penyesuaian (-) Pembayaran Biaya Kapal: " . ($pembayaran->alasan_penyesuaian ?? 'Tanpa alasan')
                        );
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error recording pembayaran biaya kapal: ' . $e->getMessage());
            return false;
        }
    }
}
