<?php
require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Boot Laravel app
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create minimal permohonan and two kontainers, then run the controller store-equivalent logic
// Clean up any prior test entries to avoid collisions
DB::table('permohonans')->where('nomor_memo', 'TEST-MEMO-STORE')->delete();

$permId = DB::table('permohonans')->insertGetId([
    'nomor_memo' => 'TEST-MEMO-STORE',
    'kegiatan' => 'pengiriman',
    'vendor_perusahaan' => 'ZONA',
    'tanggal_memo' => now()->toDateString(),
    'status' => 'Pending',
    // required fields on permohonans table
    'ukuran' => '20',
    'tujuan' => 'Pelabuhan X',
    'jumlah_kontainer' => 2,
    'created_at' => now(),
    'updated_at' => now(),
]);

// Create two kontainers and attach to permohonan via pivot
// Insert into `kontainers` because permohonan_kontainers.kontainer_id references kontainers.id
// Insert kontainers idempotently: reuse if nomor_seri_gabungan exists
$existing1 = DB::table('kontainers')->where('nomor_seri_gabungan', 'T20-TEST')->first();
if ($existing1) {
    $k1 = $existing1->id;
} else {
    $k1 = DB::table('kontainers')->insertGetId([
        'awalan_kontainer' => 'T',
        'nomor_seri_kontainer' => '20TEST',
        'akhiran_kontainer' => 'A',
        'nomor_seri_gabungan' => 'T20-TEST',
        'ukuran' => '20',
        'tipe_kontainer' => 'Dry',
        'tanggal_masuk_sewa' => now()->toDateString(),
        'pemilik_kontainer' => 'ZONA',
        'status' => 'Disewa',
        'harga_satuan' => 100000.00,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

$existing2 = DB::table('kontainers')->where('nomor_seri_gabungan', 'T40-TEST')->first();
if ($existing2) {
    $k2 = $existing2->id;
} else {
    $k2 = DB::table('kontainers')->insertGetId([
        'awalan_kontainer' => 'T',
        'nomor_seri_kontainer' => '40TEST',
        'akhiran_kontainer' => 'B',
        'nomor_seri_gabungan' => 'T40-TEST',
        'ukuran' => '40',
        'tipe_kontainer' => 'Dry',
        'tanggal_masuk_sewa' => now()->toDateString(),
        'pemilik_kontainer' => 'ZONA',
        'status' => 'Disewa',
        'harga_satuan' => 200000.00,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

DB::table('permohonan_kontainers')->insert([
    ['permohonan_id' => $permId, 'kontainer_id' => $k1],
    ['permohonan_id' => $permId, 'kontainer_id' => $k2],
]);

// Mimic approval request data
$validated = [
    'status_permohonan' => 'selesai',
    'tanggal_masuk_sewa' => now()->toDateString(),
    'tanggal_selesai_sewa' => Carbon::parse(now())->addMonth()->toDateString(),
];

// Load permohonan and its kontainers via DB queries (simple arrays)
$perm = DB::table('permohonans')->where('id', $permId)->first();
// Read the kontainers we just inserted from the `kontainers` table (correct table)
$kontainers = DB::table('kontainers')->whereIn('id', [$k1, $k2])->get();

// Compute aggregated values (copy of controller logic) but using the correct column names
$vendorValue = $perm->vendor_perusahaan ?: ($kontainers->first()->pemilik_kontainer ?? 'UNKNOWN');
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

$dateForTagihan = isset($validated['tanggal_masuk_sewa'])
    ? Carbon::parse($validated['tanggal_masuk_sewa'])->addDay()->toDateString()
    : now()->addDay()->toDateString();

$existing = DB::table('tagihan_kontainer_sewa')
    ->where('vendor', $vendorValue)
    ->where('tanggal_harga_awal', $dateForTagihan)
    ->where(function($q){ $q->whereNull('tarif')->orWhere('tarif','<>','Pranota'); })
    ->orderBy('id','desc')
    ->first();

if ($existing) {
    // merge
    $newDpp = round((float)$existing->dpp + round($aggDpp,2), 2);
    $newPpn = round((float)$existing->ppn + round($aggPpn,2), 2);
    $newPph = round((float)$existing->pph + round($aggPph,2), 2);
    $newGrand = round((float)$existing->grand_total + round($aggGrand,2), 2);
    $existingKontainers = trim((string)$existing->nomor_kontainer);
    $combinedKontainers = $existingKontainers === '' ? implode(',', $nomorKontainers) : ($existingKontainers . ',' . implode(',', $nomorKontainers));

    $existingSizes = array_filter(array_map('trim', explode(',', (string)$existing->ukuran_kontainer)));
    $mergedSizes = array_values(array_unique(array_filter(array_merge($existingSizes, $sizeList))));
    $mergedUkuran = count($mergedSizes) > 1 ? implode(',', $mergedSizes) : ($mergedSizes[0] ?? null);

    DB::table('tagihan_kontainer_sewa')->where('id', $existing->id)->update([
        'dpp' => $newDpp,
        'ppn' => $newPpn,
        'pph' => $newPph,
        'grand_total' => $newGrand,
        'harga' => $newGrand,
        'nomor_kontainer' => $combinedKontainers,
        'ukuran_kontainer' => $mergedUkuran,
        'masa' => (function() use ($existing) {
            try {
                $start = isset($existing->tanggal_harga_awal) ? Carbon::parse($existing->tanggal_harga_awal) : null;
                $masaStart = $start ? $start->locale('id')->isoFormat('D MMMM') : null;
                $masaEnd = null;
                if (!empty($existing->tanggal_harga_akhir)) {
                    $end = Carbon::parse($existing->tanggal_harga_akhir)->subDay();
                    $masaEnd = $end->locale('id')->isoFormat('D MMMM');
                } else {
                    $p = isset($existing->periode) ? intval($existing->periode) : 0;
                    if ($start && $p > 0) {
                        $derived = $start->copy()->addMonths($p)->subDay();
                        $masaEnd = $derived->locale('id')->isoFormat('D MMMM');
                    }
                }
                if ($masaStart && $masaEnd) return $masaStart . ' - ' . $masaEnd;
                return $masaStart ?: ($masaEnd ?: null);
            } catch (\Exception $e) { return null; }
        })(),
        'updated_at' => now(),
    ]);
    $tagihanId = $existing->id;
    echo "Reused existing tagihan id={$tagihanId}\n";
} else {
    $masaForInsert = (function() use ($dateForTagihan) {
        try {
            $s = Carbon::parse($dateForTagihan);
            $p = 1;
            $e = $s->copy()->addMonths($p)->subDay();
            return $s->locale('id')->isoFormat('D MMMM') . ' - ' . $e->locale('id')->isoFormat('D MMMM');
        } catch (\Exception $e) { return null; }
    })();

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
        'group_code' => 'A' . str_pad((string)$permId, 3, '0', STR_PAD_LEFT),
        'periode' => '1',
        'keterangan' => 'Tagihan dibuat dari persetujuan permohonan TEST',
        'masa' => $masaForInsert,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "Created tagihan id={$tagihanId}\n";
}

// attach pivots
foreach ([$k1, $k2] as $kid) {
    try {
        DB::table('tagihan_kontainer_sewa_kontainers')->insert([
            'tagihan_id' => $tagihanId,
            'kontainer_id' => $kid,
        ]);
    } catch (\Exception $e) {}
}

// Print the resulting tagihan row
$tag = DB::table('tagihan_kontainer_sewa')->where('id', $tagihanId)->first();
print_r($tag);

// Clean up test permohonan/kontainer? keep it for inspection

echo "Done.\n";
