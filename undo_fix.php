<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the records that we reverted to 'aktif' in the last 1 hour
$updatedProspeks = \App\Models\Prospek::where('status', 'aktif')
    ->where('updated_at', '>=', now()->subMinutes(60))
    ->get();

$count = 0;
foreach ($updatedProspeks as $prospek) {
    // Only revert those older than let's say May 2026, OR just revert all of them.
    // The user's point is that old data shouldn't be 'aktif'.
    // To be safe, I'll revert all the 585 we changed.
    $prospek->update(['status' => 'sudah_muat']);
    $count++;
}

echo "Berhasil mengembalikan $count data Prospek kembali menjadi 'sudah_muat'.\n";
