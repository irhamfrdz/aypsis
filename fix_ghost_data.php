<?php
$nomor = 'BTJ0726000109';
$oldTransactions = \App\Models\CoaTransaction::where('nomor_referensi', $nomor)->get();
foreach ($oldTransactions as $trans) {
    $akun = \App\Models\Coa::find($trans->coa_id);
    if ($akun && isset($akun->posisi_normal)) {
        if ($akun->posisi_normal === 'debit') {
            $akun->saldo_sekarang = $akun->saldo_sekarang - $trans->debit + $trans->kredit;
        } else {
            $akun->saldo_sekarang = $akun->saldo_sekarang + $trans->debit - $trans->kredit;
        }
        $akun->save();
    }
    $trans->delete();
}
echo "Fixed ghost data for {$nomor}";
