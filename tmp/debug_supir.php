<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Cek koneksi DB yang dipakai
$connection = config('database.default');
echo "Koneksi DB: {$connection}\n";

// Cek count dengan kondisi persis
$count = DB::table('surat_jalans')
    ->whereRaw("LOWER(supir) = 'nur cece'")
    ->count();
echo "Count LOWER(supir) = 'nur cece': {$count}\n";

$count2 = DB::table('surat_jalans')
    ->where('supir', 'nur cece')
    ->count();
echo "Count supir = 'nur cece' (exact): {$count2}\n";

// Coba update langsung satu baris (id=131)
$updated = DB::table('surat_jalans')
    ->where('id', 131)
    ->where('supir', 'nur cece')
    ->update(['supir' => 'NUR CECE']);
echo "Update id=131 exact match: {$updated}\n";

// Cek hasilnya
$row = DB::table('surat_jalans')->where('id', 131)->select('id', 'supir')->first();
echo "id=131 supir sekarang: '{$row->supir}'\n";
