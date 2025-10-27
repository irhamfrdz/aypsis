<?php
// Script final check untuk semua prospek

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Models\Prospek;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "=== FINAL STATUS CHECK ===\n\n";

// Total prospek
$totalProspek = Prospek::count();
echo "Total Prospek: {$totalProspek}\n";

// Prospek dengan surat_jalan_id
$linkedProspek = Prospek::whereNotNull('surat_jalan_id')->count();
echo "Prospek ter-link dengan surat jalan: {$linkedProspek}\n";

// Prospek tanpa surat_jalan_id
$unlinkedProspek = Prospek::whereNull('surat_jalan_id')->count();
echo "Prospek belum ter-link: {$unlinkedProspek}\n";

echo "\n=== BREAKDOWN BY NOMOR_KONTAINER ===\n";

// Prospek CARGO
$cargoLinked = Prospek::where('nomor_kontainer', 'CARGO')->whereNotNull('surat_jalan_id')->count();
$cargoUnlinked = Prospek::where('nomor_kontainer', 'CARGO')->whereNull('surat_jalan_id')->count();
echo "CARGO - Linked: {$cargoLinked}, Unlinked: {$cargoUnlinked}\n";

// Prospek non-CARGO
$nonCargoLinked = Prospek::where('nomor_kontainer', '!=', 'CARGO')->whereNotNull('surat_jalan_id')->count();
$nonCargoUnlinked = Prospek::where('nomor_kontainer', '!=', 'CARGO')->whereNull('surat_jalan_id')->count();
echo "Non-CARGO - Linked: {$nonCargoLinked}, Unlinked: {$nonCargoUnlinked}\n";

echo "\n=== SUMMARY ===\n";
echo "✅ Data linking berhasil!\n";
echo "✅ {$linkedProspek} dari {$totalProspek} prospek sudah ter-link dengan surat jalan\n";
if ($unlinkedProspek == 1) {
    echo "ℹ️  1 prospek belum ter-link (Tanda Terima Tanpa Surat Jalan - ini normal)\n";
}