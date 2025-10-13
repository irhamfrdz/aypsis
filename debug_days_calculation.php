<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

echo "=== DEBUG PERHITUNGAN HARI ===\n\n";

$tagihan = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'INKU6744298')
    ->where('periode', 6)
    ->first();

echo "📋 DATA MASA: {$tagihan->masa}\n";
echo "📋 TANGGAL AWAL: {$tagihan->tanggal_awal}\n";
echo "📋 TANGGAL AKHIR: {$tagihan->tanggal_akhir}\n\n";

// Test logic command
function calculateDaysFromTagihan($tagihan): int {
    // If masa contains date range, parse it
    if ($tagihan->masa && strpos($tagihan->masa, ' - ') !== false) {
        echo "🔍 Parsing dari field 'masa'...\n";
        try {
            $parts = explode(' - ', $tagihan->masa);
            if (count($parts) === 2) {
                echo "Part 1: '{$parts[0]}'\n";
                echo "Part 2: '{$parts[1]}'\n";

                $startDate = Carbon::parse($parts[0]);
                $endDate = Carbon::parse($parts[1]);
                $days = $startDate->diffInDays($endDate) + 1;

                echo "Start: {$startDate->format('d-m-Y')}\n";
                echo "End: {$endDate->format('d-m-Y')}\n";
                echo "Days: {$days}\n";

                return $days;
            }
        } catch (\Exception $e) {
            echo "❌ Error parsing masa: " . $e->getMessage() . "\n";
        }
    }

    echo "🔍 Fallback ke tanggal database...\n";
    // Fall back to tanggal_awal and tanggal_akhir
    if ($tagihan->tanggal_awal && $tagihan->tanggal_akhir) {
        try {
            $startDate = Carbon::parse($tagihan->tanggal_awal);
            $endDate = Carbon::parse($tagihan->tanggal_akhir);
            $days = $startDate->diffInDays($endDate) + 1;

            echo "Start: {$startDate->format('d-m-Y')}\n";
            echo "End: {$endDate->format('d-m-Y')}\n";
            echo "Days: {$days}\n";

            return $days;
        } catch (\Exception $e) {
            echo "❌ Error parsing database dates: " . $e->getMessage() . "\n";
        }
    }

    return 30; // fallback
}

$calculatedDays = calculateDaysFromTagihan($tagihan);
echo "\n📊 HASIL PERHITUNGAN HARI: {$calculatedDays}\n";

echo "\n=== MANUAL CHECK ===\n";
echo "42,042 × 19 hari = " . number_format(42042 * 19, 0, ',', '.') . "\n";
echo "42,042 × 20 hari = " . number_format(42042 * 20, 0, ',', '.') . "\n";

echo "\n=== SELESAI ===\n";
