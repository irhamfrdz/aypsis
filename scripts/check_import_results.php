<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\DaftarTagihanKontainerSewa;

// show top groups by count
$rows = DaftarTagihanKontainerSewa::select('vendor','tanggal_awal','group','periode',\DB::raw('count(*) as cnt'))
    ->groupBy('vendor','tanggal_awal','group','periode')
    ->orderBy('cnt','desc')
    ->limit(12)->get();
foreach ($rows as $r) {
    echo "vendor={$r->vendor} tanggal_awal={$r->tanggal_awal} group={$r->group} periode={$r->periode} cnt={$r->cnt}\n";
}

// show some ZONA rows
$zona = DaftarTagihanKontainerSewa::where('vendor','like','%ZONA%')->limit(10)->get();
foreach ($zona as $z) {
    echo "ZONA sample: nomor={$z->nomor_kontainer} vendor={$z->vendor} tanggal_awal={$z->tanggal_awal} group={$z->group} periode={$z->periode}\n";
}

// counts
$total = DaftarTagihanKontainerSewa::count();
echo "Total rows in DB: $total\n";
