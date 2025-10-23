<?php<?php<?php



require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();require_once 'vendor/autoload.php';require_once 'bootstrap/app.php';



echo "🔍 Checking pranota PSJ-1025-000008...\n";$app = require_once 'bootstrap/app.php';



// Check pranota status$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();$app = require_once 'bootstrap/app.php';

$pranota = DB::table('pranota_surat_jalans')->where('nomor_pranota', 'PSJ-1025-000008')->first();

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

if ($pranota) {

    echo "✅ Pranota found:\n";echo "🔍 Checking pranota PSJ-1025-000008...\n";

    echo "ID: {$pranota->id}\n";

    echo "Status Pranota: {$pranota->status_pranota}\n";use App\Models\Pranota;

    echo "Status Pembayaran: " . ($pranota->status_pembayaran ?? 'NULL') . "\n";

    echo "Total Amount: Rp " . number_format((float)$pranota->total_amount) . "\n";// Check pranota status

    echo "\n";

$pranota = DB::table('pranota_surat_jalans')->where('nomor_pranota', 'PSJ-1025-000008')->first();echo "Checking Pranota records:\n";

    // Check pembayaran records for this pranota

    echo "🔍 Checking pembayaran records...\n";$pranotaList = Pranota::all();

    $pembayaran = DB::table('pembayaran_pranota_surat_jalan')->where('pranota_surat_jalan_id', $pranota->id)->get();

    if ($pranota) {

    if ($pembayaran->count() > 0) {

        echo "✅ Found {$pembayaran->count()} pembayaran record(s):\n";    echo "✅ Pranota found:\n";foreach ($pranotaList as $pranota) {

        foreach ($pembayaran as $pay) {

            echo "- ID: {$pay->id}, Nomor: {$pay->nomor_pembayaran}, Total: Rp " . number_format((float)$pay->total_tagihan_setelah_penyesuaian) . ", Tanggal: {$pay->tanggal_pembayaran}\n";    echo "ID: {$pranota->id}\n";    echo "ID: {$pranota->id}, Status: {$pranota->status}, No Invoice: {$pranota->no_invoice}\n";

        }

    } else {    echo "Status Pranota: {$pranota->status_pranota}\n";}

        echo "❌ No pembayaran records found for this pranota\n";

    }    echo "Status Pembayaran: " . ($pranota->status_pembayaran ?? 'NULL') . "\n";



    // Check surat jalan status    echo "Total Amount: Rp " . number_format($pranota->total_amount) . "\n";echo "\nTotal Pranota: " . $pranotaList->count() . "\n";

    echo "\n🔍 Checking related surat jalan status...\n";

    $suratJalans = DB::table('pranota_surat_jalan_items')    echo "\n";echo "Belum Lunas: " . Pranota::where('status', 'Belum Lunas')->count() . "\n";

        ->join('surat_jalans', 'pranota_surat_jalan_items.surat_jalan_id', '=', 'surat_jalans.id')

        ->where('pranota_surat_jalan_items.pranota_surat_jalan_id', $pranota->id)echo "Unpaid: " . Pranota::where('status', 'unpaid')->count() . "\n";

        ->select('surat_jalans.id', 'surat_jalans.no_surat_jalan', 'surat_jalans.status_pembayaran')

        ->get();    // Check pembayaran records for this pranota

    echo "🔍 Checking pembayaran records...\n";

    if ($suratJalans->count() > 0) {    $pembayaran = DB::table('pembayaran_pranota_surat_jalan')->where('pranota_surat_jalan_id', $pranota->id)->get();

        echo "✅ Related surat jalan status:\n";

        foreach ($suratJalans as $sj) {    if ($pembayaran->count() > 0) {

            echo "- {$sj->no_surat_jalan}: " . ($sj->status_pembayaran ?? 'NULL') . "\n";        echo "✅ Found {$pembayaran->count()} pembayaran record(s):\n";

        }        foreach ($pembayaran as $pay) {

    } else {            echo "- ID: {$pay->id}, Nomor: {$pay->nomor_pembayaran}, Total: Rp " . number_format($pay->total_tagihan_setelah_penyesuaian) . ", Tanggal: {$pay->tanggal_pembayaran}\n";

        echo "❌ No related surat jalan found\n";        }

    }    } else {

        echo "❌ No pembayaran records found for this pranota\n";

} else {    }

    echo "❌ Pranota PSJ-1025-000008 not found\n";

}    // Check surat jalan status
    echo "\n🔍 Checking related surat jalan status...\n";
    $suratJalans = DB::table('pranota_surat_jalan_items')
        ->join('surat_jalans', 'pranota_surat_jalan_items.surat_jalan_id', '=', 'surat_jalans.id')
        ->where('pranota_surat_jalan_items.pranota_surat_jalan_id', $pranota->id)
        ->select('surat_jalans.id', 'surat_jalans.no_surat_jalan', 'surat_jalans.status_pembayaran')
        ->get();

    if ($suratJalans->count() > 0) {
        echo "✅ Related surat jalan status:\n";
        foreach ($suratJalans as $sj) {
            echo "- {$sj->no_surat_jalan}: {$sj->status_pembayaran}\n";
        }
    } else {
        echo "❌ No related surat jalan found\n";
    }

} else {
    echo "❌ Pranota PSJ-1025-000008 not found\n";
}
