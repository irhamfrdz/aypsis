<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$records = App\Models\SuratJalanBongkaranBatam::orderBy('id', 'desc')->take(20)->get();
$pricelistItems = \App\Models\PricelistUangJalanBatam::activeBbm()->get();

foreach($records as $r) {
    if ($r->lokasi === 'batam' && floatval($r->uang_jalan_nominal) == 0 && !empty($r->tujuan_pengambilan)) {
        // Find matching uang jalan
        $matchedItem = null;
        $rowTujuan = strtolower(trim($r->tujuan_pengambilan));
        foreach ($pricelistItems as $item) {
            if (!$item->wilayah) continue;
            $subWilayahs = array_map(function($w) { return strtolower(trim($w)); }, explode(',', $item->wilayah));
            if (in_array($rowTujuan, $subWilayahs)) {
                $matchedItem = $item;
                $idx = array_search($rowTujuan, $subWilayahs);
                $originalSubWilayahs = array_map('trim', explode(',', $item->wilayah));
                $r->tujuan_pengambilan = $originalSubWilayahs[$idx];
                $r->tujuan_pengiriman = $originalSubWilayahs[$idx];
                break;
            }
        }
        
        if ($matchedItem) {
            $is20ft = true;
            if (!empty($r->size)) {
                $normSize = strtolower(str_replace(' ', '', $r->size));
                if (strpos($normSize, '40') !== false) {
                    $is20ft = false;
                }
            }
            $isFull = (strtolower($r->f_e) === 'full' || strtolower($r->f_e) === 'f');
            
            if ($is20ft) {
                $r->uang_jalan_nominal = $isFull ? ($matchedItem->tarif_20ft_full ?? 0) : ($matchedItem->tarif_20ft_empty ?? 0);
            } else {
                $r->uang_jalan_nominal = $isFull ? ($matchedItem->tarif_40ft_full ?? 0) : ($matchedItem->tarif_40ft_empty ?? 0);
            }
            $r->ring = (string) $matchedItem->ring;
            $r->save();
            echo "Updated SJ " . $r->nomor_surat_jalan . " - Nominal: " . $r->uang_jalan_nominal . "\n";
        }
    }
}
