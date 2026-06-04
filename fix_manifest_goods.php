<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Manifest;
use Illuminate\Support\Facades\DB;

echo "=== Syncing Manifest Goods Names ===\n";

$manifests = Manifest::whereNotNull('prospek_id')->with('prospek.tandaTerima')->get();
echo "Found " . $manifests->count() . " manifests to check.\n";

$updatedCount = 0;

foreach ($manifests as $manifest) {
    $prospek = $manifest->prospek;
    if (!$prospek) continue;

    $tt = $prospek->tandaTerima;
    if (!$tt) continue;

    // Extract item names
    $itemNames = [];
    if (!empty($tt->dimensi_items) && is_array($tt->dimensi_items)) {
        foreach ($tt->dimensi_items as $item) {
            if (!empty($item['nama_barang'])) {
                $itemNames[] = $item['nama_barang'];
            }
        }
    } elseif (!empty($tt->dimensi_details) && is_array($tt->dimensi_details)) {
        foreach ($tt->dimensi_details as $item) {
            if (!empty($item['nama_barang'])) {
                $itemNames[] = $item['nama_barang'];
            }
        }
    } elseif (!empty($tt->nama_barang)) {
        if (is_array($tt->nama_barang)) {
            $itemNames = $tt->nama_barang;
        } elseif (is_string($tt->nama_barang) && $tt->nama_barang !== 'null') {
            $itemNames[] = $tt->nama_barang;
        }
    }

    $correctName = !empty($itemNames) ? implode(', ', $itemNames) : ($tt->jenis_barang ?: null);

    if ($correctName && $manifest->nama_barang !== $correctName) {
        echo "Manifest ID {$manifest->id} (TT: {$manifest->nomor_tanda_terima}):\n";
        echo "  - Old name: '{$manifest->nama_barang}'\n";
        echo "  - New name: '{$correctName}'\n";
        
        $manifest->update(['nama_barang' => $correctName]);
        $updatedCount++;
    }
}

echo "\nDone! Total manifests updated: $updatedCount\n";
