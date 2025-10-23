<?php
// Debug pergerakan kapal data
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Use Laravel's environment
$app->bootstrapWith([
    \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
    \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
    \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
    \Illuminate\Foundation\Bootstrap\BootProviders::class,
]);

use App\Models\PergerakanKapal;
use Illuminate\Support\Facades\Schema;

echo "=== DEBUG PERGERAKAN KAPAL DATA ===\n\n";

// 1. Check table structure
echo "1. Table structure:\n";
$columns = Schema::getColumnListing('pergerakan_kapal');
foreach ($columns as $column) {
    echo "   - $column\n";
}
echo "\n";

// 2. Check sample data
echo "2. Sample pergerakan kapal data:\n";
$pergerakan = PergerakanKapal::first();
if ($pergerakan) {
    echo "   ID: {$pergerakan->id}\n";
    echo "   Voyage: " . ($pergerakan->voyage ?? 'NULL') . "\n";
    echo "   Nama Kapal: " . ($pergerakan->nama_kapal ?? 'NULL') . "\n";
    echo "   Kapten: " . ($pergerakan->kapten ?? 'NULL') . "\n";
    echo "   Tanggal Sandar: " . ($pergerakan->tanggal_sandar ?? 'NULL') . "\n";
    echo "   Tanggal Berangkat: " . ($pergerakan->tanggal_berangkat ?? 'NULL') . "\n";
    echo "   Pelabuhan Asal: " . ($pergerakan->pelabuhan_asal ?? 'NULL') . "\n";
    echo "   Pelabuhan Tujuan: " . ($pergerakan->pelabuhan_tujuan ?? 'NULL') . "\n";
    echo "   Status: " . ($pergerakan->status ?? 'NULL') . "\n";
} else {
    echo "   No pergerakan kapal found\n";
}
echo "\n";

// 3. Check available voyages query
echo "3. Available voyages query (same as controller):\n";
$availableVoyages = PergerakanKapal::whereNotIn('id', function($query) {
    $query->select('pergerakan_kapal_id')
          ->from('prospek_kapal')
          ->whereNotNull('pergerakan_kapal_id');
})
->where('status', '!=', 'cancelled')
->orderBy('tanggal_sandar', 'desc')
->get();

echo "   Found " . $availableVoyages->count() . " available voyages\n";
if ($availableVoyages->count() > 0) {
    $voyage = $availableVoyages->first();
    echo "   First voyage:\n";
    echo "     ID: {$voyage->id}\n";
    echo "     Voyage: " . ($voyage->voyage ?? 'NULL') . "\n";
    echo "     Nama Kapal: " . ($voyage->nama_kapal ?? 'NULL') . "\n";
    echo "     Kapten: " . ($voyage->kapten ?? 'NULL') . "\n";
    echo "     Tanggal Sandar: " . ($voyage->tanggal_sandar ?? 'NULL') . "\n";
    echo "     Tanggal Berangkat: " . ($voyage->tanggal_berangkat ?? 'NULL') . "\n";
}
echo "\n";

// 4. Check all pergerakan kapal with details
echo "4. All pergerakan kapal with key fields:\n";
$allPergerakan = PergerakanKapal::select('id', 'voyage', 'nama_kapal', 'kapten', 'tanggal_sandar', 'tanggal_berangkat', 'status')
    ->limit(5)
    ->get();

foreach ($allPergerakan as $item) {
    echo "   ID {$item->id}: {$item->voyage} | Kapten: " . ($item->kapten ?? 'NULL') .
         " | Sandar: " . ($item->tanggal_sandar ?? 'NULL') .
         " | Berangkat: " . ($item->tanggal_berangkat ?? 'NULL') . "\n";
}
echo "\n";

// 5. Check if columns exist
echo "5. Column existence check:\n";
$requiredColumns = ['kapten', 'tanggal_sandar', 'tanggal_berangkat'];
foreach ($requiredColumns as $column) {
    $exists = Schema::hasColumn('pergerakan_kapal', $column);
    echo "   Column '$column': " . ($exists ? 'EXISTS' : 'MISSING') . "\n";
}
echo "\n";

echo "=== ANALYSIS ===\n";
echo "If kapten, tanggal_sandar, tanggal_berangkat show as '-', possible causes:\n";
echo "1. Columns don't exist in database\n";
echo "2. Data is NULL in database\n";
echo "3. Column names are different\n";
echo "4. JavaScript is not receiving the data correctly\n";
