<?php
require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Boot Laravel app
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a permohonan with unique memo for this test
$memo = 'TEST-MEMO-STORE-5-' . date('YmdHis');
DB::table('permohonans')->where('nomor_memo', $memo)->delete();
$permId = DB::table('permohonans')->insertGetId([
    'nomor_memo' => $memo,
    'kegiatan' => 'pengiriman',
    'vendor_perusahaan' => 'ZONA',
    'tanggal_memo' => now()->toDateString(),
    'status' => 'Pending',
    'ukuran' => 'mixed',
    'tujuan' => 'Pelabuhan Test',
    'jumlah_kontainer' => 5,
    'created_at' => now(),
    'updated_at' => now(),
]);

// sizes to insert
$sizes = ['20','40','45','10','30'];
$kontainerIds = [];

foreach ($sizes as $i => $size) {
    $serial = "T{$size}-TEST-" . ($i+1);
    $existing = DB::table('kontainers')->where('nomor_seri_gabungan', $serial)->first();
    if ($existing) {
        $kid = $existing->id;
    } else {
        $kid = DB::table('kontainers')->insertGetId([
            'awalan_kontainer' => 'T',
            'nomor_seri_kontainer' => substr($serial,1,6),
            'akhiran_kontainer' => chr(65 + $i),
            'nomor_seri_gabungan' => $serial,
            'ukuran' => $size,
            'tipe_kontainer' => 'Dry',
            'tanggal_masuk_sewa' => now()->toDateString(),
            'pemilik_kontainer' => 'ZONA',
            'status' => 'Disewa',
            'harga_satuan' => 100000 + ($i * 50000),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    $kontainerIds[] = $kid;
}

// attach to permohonan
$rows = [];
foreach ($kontainerIds as $kid) {
    $rows[] = ['permohonan_id' => $permId, 'kontainer_id' => $kid];
}
DB::table('permohonan_kontainers')->insert($rows);

// Prepare validated-like input and force a future date so we create a new tagihan
$validated = [
    'status_permohonan' => 'selesai',
    'tanggal_masuk_sewa' => now()->toDateString(),
];

$dateForTagihan = Carbon::parse($validated['tanggal_masuk_sewa'])->addDays(3)->toDateString();

// Read kontainers inserted
$kontainers = DB::table('kontainers')->whereIn('id', $kontainerIds)->get();

$vendorValue = 'ZONA';
$sizeList = collect($kontainers)->pluck('ukuran')->filter()->unique()->values()->all();
$ukuran = count($sizeList) > 1 ? implode(',', $sizeList) : ($sizeList[0] ?? 'UNKNOWN');

$aggDpp = 0.0; $aggPpn = 0.0; $aggPph = 0.0; $aggGrand = 0.0;
$nomorKontainers = [];
foreach ($kontainers as $k) {
    $base = floatval($k->harga_satuan ?? 0);
    $dpp = round(($base * 11) / 12, 2);
    $ppn = round($dpp * 0.12, 2);
    $pph = round($base * 0.02, 2);
    $grand = round($base + $ppn - $pph, 2);
    $aggDpp += $dpp; $aggPpn += $ppn; $aggPph += $pph; $aggGrand += $grand;
    $nomorKontainers[] = $k->nomor_seri_gabungan ?? ('id:' . $k->id);
}

// Ensure no existing tagihan for vendor + date (delete to be safe for test)
DB::table('tagihan_kontainer_sewa')->where('vendor', $vendorValue)->where('tanggal_harga_awal', $dateForTagihan)->delete();

$masaForInsert = (function() use ($dateForTagihan) {
    try {
        $s = Carbon::parse($dateForTagihan);
        $p = 1;
        $e = $s->copy()->addMonths($p)->subDay();
        return $s->locale('id')->isoFormat('D MMMM') . ' - ' . $e->locale('id')->isoFormat('D MMMM');
    } catch (\Exception $e) { return null; }
})();

// compute next group code as A001 when table empty
$maxId = DB::table('tagihan_kontainer_sewa')->max('id');
$nextSeq = $maxId ? ($maxId + 1) : 1;
$groupCode = 'A' . str_pad((string)$nextSeq, 3, '0', STR_PAD_LEFT);

$tagihanId = DB::table('tagihan_kontainer_sewa')->insertGetId([
    'vendor' => $vendorValue,
    'tarif' => 'Bulanan',
    'ukuran_kontainer' => $ukuran,
    'harga' => round($aggGrand,2),
    'dpp' => round($aggDpp,2),
    'ppn' => round($aggPpn,2),
    'pph' => round($aggPph,2),
    'grand_total' => round($aggGrand,2),
    'nomor_kontainer' => implode(',', $nomorKontainers),
    'tanggal_harga_awal' => $dateForTagihan,
    'group_code' => $groupCode,
    'periode' => '1',
    'keterangan' => 'Tagihan dibuat dari persetujuan permohonan TEST-5',
    'masa' => $masaForInsert,
    'created_at' => now(),
    'updated_at' => now(),
]);

foreach ($kontainerIds as $kid) {
    try { DB::table('tagihan_kontainer_sewa_kontainers')->insert(['tagihan_id' => $tagihanId, 'kontainer_id' => $kid]); } catch (\Exception $e) {}
}

$tag = DB::table('tagihan_kontainer_sewa')->where('id', $tagihanId)->first();
print_r($tag);

echo "Done.\n";
