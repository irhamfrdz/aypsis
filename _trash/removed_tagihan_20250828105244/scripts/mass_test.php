<?php

// scripts/mass_test.php
// Bootstraps Laravel and runs a minimal massProcess simulation for permohonan id 6.

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Permohonan;
use App\Models\TagihanKontainerSewa;

$id = 6;
try {
    $perm = Permohonan::find($id);
    if (! $perm) {
        echo "Permohonan $id not found\n";
        exit(1);
    }

    $kontainerIds = $perm->kontainers()->pluck('kontainers.id')->toArray();
    echo "Kontainer IDs for permohonan $id: ".implode(',', $kontainerIds)."\n";

    // Find or create a Tagihan for vendor/date using simple heuristic (vendor from permohonan)
    $vendor = $perm->vendor ?? 'ZONA';
    $tanggal = now()->toDateString();

    $tag = TagihanKontainerSewa::first();
    if (! $tag) {
        $tag = TagihanKontainerSewa::create([
            'vendor' => $vendor,
            'tarif' => 'Bulanan',
            'ukuran_kontainer' => '20',
            'harga' => 100000,
            'tanggal_harga_awal' => now()->startOfMonth()->toDateString(),
            'tanggal_harga_akhir' => now()->endOfMonth()->toDateString(),
        ]);
        echo "Created Tagihan ID: {$tag->id}\n";
    } else {
        echo "Using existing Tagihan ID: {$tag->id}\n";
    }

    // Try to sync kontainers
    $tag->kontainers()->syncWithoutDetaching($kontainerIds);
    echo "Synced kontainers to Tagihan {$tag->id}\n";

    // Show pivot rows count
    $rows = \DB::table('tagihan_kontainer_sewa_kontainers')->where('tagihan_id', $tag->id)->get();
    echo "Pivot rows for tagihan {$tag->id}: " . $rows->count() . "\n";

} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}


