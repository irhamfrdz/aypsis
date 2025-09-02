<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Boot the framework
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

$items = TagihanKontainerSewa::orderBy('created_at', 'desc')->take(10)->get();
if ($items->isEmpty()) {
    echo "No tagihan records found.\n";
    exit(0);
}

foreach ($items as $i => $item) {
    $vendor = $item->vendor;
    $tanggal = $item->tanggal_harga_awal;
    // show raw DB value for tanggal
    $raw = DB::table('tagihan_kontainer_sewa')->where('id', $item->id)->value('tanggal_harga_awal');

    $count = DB::table('permohonans')
        ->where('vendor_perusahaan', $vendor)
        ->whereDate('tanggal_memo', $tanggal)
        ->count();

    $total = DB::table('permohonans')
        ->where('vendor_perusahaan', $vendor)
        ->whereDate('tanggal_memo', $tanggal)
        ->selectRaw('COALESCE(SUM(COALESCE(total_harga_setelah_adj, jumlah_uang_jalan)),0) as total')
        ->value('total');

    echo "#".($i+1)." id={$item->id} vendor={$vendor} tanggal(cast)={$tanggal} tanggal(raw)={$raw} count={$count} total={$total}\n";
}
