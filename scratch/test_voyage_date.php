<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$namaKapal = 'KM. ALKEN PRINCESS';

$normalizedKapal = strtolower(trim(preg_replace('/[.\s]+/', ' ', $namaKapal)));
$allKeywords = explode(' ', $normalizedKapal);
$ignorePrefixes = ['km', 'mv', 'mt', 'tb', 'spob', 'klm', 'lp', 'mp'];

$keywords = array_filter($allKeywords, function ($word) use ($ignorePrefixes) {
    return ! in_array($word, $ignorePrefixes);
});
if (empty($keywords)) {
    $keywords = $allKeywords;
}
$keywords = array_values($keywords);

$voyagesFromNaikKapalQuery = DB::table('naik_kapal')
    ->select('no_voyage', DB::raw('MIN(COALESCE(tanggal_muat, created_at)) as tanggal'))
    ->whereNotNull('no_voyage')
    ->where('no_voyage', '!=', '');

$voyagesFromNaikKapalQuery->where(function ($q) use ($keywords) {
    foreach ($keywords as $keyword) {
        $q->where('nama_kapal', 'like', "%{$keyword}%");
    }
});
$voyagesFromNaikKapal = $voyagesFromNaikKapalQuery->groupBy('no_voyage')->get();

$voyagesFromBlsQuery = DB::table('bls')
    ->select('no_voyage', DB::raw('MIN(COALESCE(tanggal_berangkat, created_at)) as tanggal'))
    ->whereNotNull('no_voyage')
    ->where('no_voyage', '!=', '');

$voyagesFromBlsQuery->where(function ($q) use ($keywords) {
    foreach ($keywords as $keyword) {
        $q->where('nama_kapal', 'like', "%{$keyword}%");
    }
});
$voyagesFromBls = $voyagesFromBlsQuery->groupBy('no_voyage')->get();

$voyageDates = [];
foreach ($voyagesFromNaikKapal as $row) {
    $voyageDates[$row->no_voyage] = $row->tanggal;
}
foreach ($voyagesFromBls as $row) {
    if (! isset($voyageDates[$row->no_voyage]) || $row->tanggal < $voyageDates[$row->no_voyage]) {
        $voyageDates[$row->no_voyage] = $row->tanggal;
    }
}
ksort($voyageDates);

$voyages = array_keys($voyageDates);
$voyagesDetailed = [];
foreach ($voyageDates as $no_voyage => $date) {
    $formattedDate = $date ? \Carbon\Carbon::parse($date)->format('d/M/Y') : '-';
    $voyagesDetailed[] = [
        'no_voyage' => $no_voyage,
        'tanggal' => $formattedDate,
    ];
}

print_r($voyagesDetailed);
