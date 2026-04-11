<?php
$data = \App\Models\Bl::select(
    \DB::raw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) as normalized_nama_kapal"),
    \DB::raw("MAX(nama_kapal) as nama_kapal"),
    'no_voyage'
)
->whereNotNull('nama_kapal')
->whereNotNull('no_voyage')
->where('nama_kapal', '!=', '')
->where('no_voyage', '!=', '')
->groupBy(\DB::raw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' '))"), 'no_voyage')
->orderBy('normalized_nama_kapal')
->orderBy('no_voyage')
->get();

$mapped = $data->groupBy('normalized_nama_kapal')->mapWithKeys(function($voyages) {
    return [$voyages->first()->nama_kapal => $voyages->pluck('no_voyage')->toArray()];
})->toArray();

dump($mapped);
