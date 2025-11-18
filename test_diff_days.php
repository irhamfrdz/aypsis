<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Carbon\Carbon;

echo "Testing diffInDays calculation:\n\n";

$startDate = Carbon::parse('2025-04-25');
$endDate = Carbon::parse('2025-05-23');

echo "Start: " . $startDate->format('Y-m-d') . " (" . $startDate->format('d M Y') . ")\n";
echo "End: " . $endDate->format('Y-m-d') . " (" . $endDate->format('d M Y') . ")\n\n";

$diffDays = $startDate->diffInDays($endDate);
echo "diffInDays: {$diffDays}\n";
echo "diffInDays + 1: " . ($diffDays + 1) . "\n\n";

// Manual count
echo "Manual verification:\n";
$current = clone $startDate;
$count = 0;
while ($current->lte($endDate)) {
    $count++;
    echo "  Day {$count}: " . $current->format('d M Y') . "\n";
    $current->addDay();
}
echo "\nTotal days (manual count): {$count}\n";

echo "\n========================================\n\n";

// Test dengan data yang ada di database
echo "Testing dengan data ZONA2938308:\n";
echo "Expected: 29 hari\n";
echo "Actual diffInDays + 1: " . ($diffDays + 1) . "\n";
echo "DPP dengan 29 hari: 22.523 × 29 = " . (22523 * 29) . "\n";
echo "DPP dengan 30 hari: 22.523 × 30 = " . (22523 * 30) . "\n";
echo "DPP di database: 675.676\n";
