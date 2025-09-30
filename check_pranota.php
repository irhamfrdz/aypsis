<?php<?php<?php



require_once 'vendor/autoload.php';require_once 'vendor/autoload.php';



$app = require_once 'bootstrap/app.php';require_once 'vendor/autoload.php';$app = require_once 'bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pranota = \App\Models\PranotaPerbaikanKontainer::whereDoesntHave('pembayaranPranotaPerbaikanKontainers')

    ->where('status', 'belum_dibayar')$app = require_once 'bootstrap/app.php';

    ->with(['perbaikanKontainers.kontainer'])

    ->first();$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();use Illuminate\Support\Facades\DB;



if ($pranota) {

    echo "ID: " . $pranota->id . PHP_EOL;

    echo "Nomor Pranota: " . ($pranota->nomor_pranota ?? 'Belum ada') . PHP_EOL;$pranota = \App\Models\PranotaPerbaikanKontainer::whereDoesntHave('pembayaranPranotaPerbaikanKontainers')try {

    echo "Perbaikan Kontainers count: " . $pranota->perbaikanKontainers->count() . PHP_EOL;

    ->where('status', 'belum_dibayar')    $count = DB::table('pranota_perbaikan_kontainers')->count();

    if ($pranota->perbaikanKontainers->count() > 0) {

        $first = $pranota->perbaikanKontainers->first();    ->with(['perbaikanKontainers.kontainer'])    echo 'Total pranota records: ' . $count . PHP_EOL;

        echo "Kontainer ID: " . ($first->kontainer_id ?? 'null') . PHP_EOL;

        echo "Kontainer Nomor: " . ($first->kontainer->nomor_kontainer ?? 'N/A') . PHP_EOL;    ->first();

        echo "Kontainer exists: " . ($first->kontainer ? 'yes' : 'no') . PHP_EOL;

    }    if($count > 0) {

} else {

    echo "No pranota found" . PHP_EOL;if ($pranota) {        $latest = DB::table('pranota_perbaikan_kontainers')->latest('created_at')->first();

}
    echo "ID: " . $pranota->id . PHP_EOL;        echo 'Latest record details:' . PHP_EOL;

    echo "Nomor Pranota: " . ($pranota->nomor_pranota ?? 'Belum ada') . PHP_EOL;        echo '- ID: ' . $latest->id . PHP_EOL;

    echo "Perbaikan Kontainers count: " . $pranota->perbaikanKontainers->count() . PHP_EOL;        echo '- Nomor Pranota: ' . ($latest->nomor_pranota ?? 'NULL') . PHP_EOL;

        echo '- Tanggal Pranota: ' . ($latest->tanggal_pranota ?? 'NULL') . PHP_EOL;

    if ($pranota->perbaikanKontainers->count() > 0) {        echo '- Status: ' . ($latest->status ?? 'NULL') . PHP_EOL;

        $first = $pranota->perbaikanKontainers->first();        echo '- Created at: ' . $latest->created_at . PHP_EOL;

        echo "Kontainer ID: " . ($first->kontainer_id ?? 'null') . PHP_EOL;    } else {

        echo "Kontainer Nomor: " . ($first->kontainer->nomor_kontainer ?? 'N/A') . PHP_EOL;        echo 'No pranota records found in database.' . PHP_EOL;

        echo "Kontainer exists: " . ($first->kontainer ? 'yes' : 'no') . PHP_EOL;    }

    }

} else {    // Also check perbaikan_kontainers table

    echo "No pranota found" . PHP_EOL;    $perbaikanCount = DB::table('perbaikan_kontainers')->count();

}    echo PHP_EOL . 'Total perbaikan records: ' . $perbaikanCount . PHP_EOL;

    if($perbaikanCount > 0) {
        $latestPerbaikan = DB::table('perbaikan_kontainers')->latest('created_at')->first();
        echo 'Latest perbaikan record:' . PHP_EOL;
        echo '- ID: ' . $latestPerbaikan->id . PHP_EOL;
        echo '- Nomor Kontainer: ' . ($latestPerbaikan->nomor_kontainer ?? 'NULL') . PHP_EOL;
        echo '- Status: ' . ($latestPerbaikan->status_perbaikan ?? 'NULL') . PHP_EOL;
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>
