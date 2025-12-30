<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\SuratJalan;
use App\Models\Permohonan;

$user = User::where('username','duloh')->with('karyawan')->first();
if (!$user) {
    echo "User not found\n";
    exit;
}

echo "User: {$user->username} | name: {$user->name} | karyawan: " . ($user->karyawan->nama_lengkap ?? 'NULL') . PHP_EOL;

echo "--- Searching Surat Jalan matches ---\n";
$sjs = SuratJalan::where(function($q) use($user){
    $q->where('supir', $user->name)
      ->orWhere('supir', $user->username)
      ->orWhere('supir', $user->karyawan->nama ?? null);
})->limit(50)->get();

echo "Found SJs: " . $sjs->count() . PHP_EOL;
foreach ($sjs as $sj) {
    echo "ID: {$sj->id} | supir:[{$sj->supir}] | no: " . ($sj->no_surat_jalan ?? $sj->nomor_surat_jalan) . " | status: {$sj->status}\n";
}

echo "--- Searching Permohonan matches (supir_id) ---\n";
$ps = Permohonan::where('supir_id', $user->karyawan->id ?? 0)->limit(50)->get();

echo "Found Permohonan: " . $ps->count() . PHP_EOL;
foreach ($ps as $p) {
    echo "ID: {$p->id} | supir_id: {$p->supir_id} | nomor_memo: {$p->nomor_memo} | status: {$p->status}\n";
}
