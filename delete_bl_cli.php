<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Bl;
use Illuminate\Support\Facades\DB;

try {
    echo "Mencari data BL dengan no_voyage AP03BJ26...\n";
    $count = Bl::where('no_voyage', 'AP03BJ26')->count();
    echo "Ditemukan $count record.\n";

    if ($count > 0) {
        $deleted = DB::table('bls')->where('no_voyage', 'AP03BJ26')->delete();
        echo "$deleted record berhasil dihapus secara permanen dari table bls.\n";
    } else {
        echo "Tidak ada tindakan yang dilakukan.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
