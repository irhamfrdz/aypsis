<?php
$histories = App\Models\HistoryKontainer::where('jenis_kegiatan', 'Pindahan Gudang')
    ->whereNull('asal_gudang_id')
    ->get();

foreach ($histories as $hk) {
    if (preg_match('/Pemindahan dari (.*?) \(.*?\) ke tujuan baru/i', $hk->keterangan, $m)) {
        $gudangName = trim($m[1]);
        $g = App\Models\Gudang::where('nama_gudang', $gudangName)->first();
        if ($g) {
            $hk->asal_gudang_id = $g->id;
            $hk->save();
            echo "Fixed HK ID {$hk->id} to Gudang {$g->nama_gudang}\n";
        } else {
            echo "Gudang not found: {$gudangName} for HK ID {$hk->id}\n";
        }
    }
}
