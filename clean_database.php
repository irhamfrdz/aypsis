<?php

$host = '127.0.0.1';
$dbname = 'aypsis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== CLEANING DATABASE - REMOVING INVALID PERIODS ===\n\n";

    // Get all unique containers with their periods
    $stmt = $pdo->query("
        SELECT nomor_kontainer, vendor, tanggal_awal, tanggal_akhir,
               GROUP_CONCAT(periode ORDER BY periode) as all_periods,
               COUNT(*) as total_records
        FROM daftar_tagihan_kontainer_sewa
        GROUP BY nomor_kontainer, vendor, tanggal_awal, tanggal_akhir
        HAVING COUNT(*) > 1
        ORDER BY nomor_kontainer, vendor
    ");

    $containers = $stmt->fetchAll();
    $totalDeleted = 0;
    $containersProcessed = 0;

    echo "Found " . count($containers) . " containers with multiple periods to check...\n\n";

    foreach ($containers as $container) {
        $containersProcessed++;

        // Calculate max allowed periode
        $startDate = new DateTime($container['tanggal_awal']);
        $currentDate = new DateTime('2025-09-01'); // Current date

        if ($container['tanggal_akhir']) {
            // Container with end date
            $endDate = new DateTime($container['tanggal_akhir']);
            $interval = $startDate->diff($endDate);
            $months = ($interval->y * 12) + $interval->m;
            // Add 1 if there are remaining days
            if ($interval->d > 0) $months++;
            $maxAllowedPeriode = $months;
        } else {
            // Ongoing container
            $interval = $startDate->diff($currentDate);
            $months = ($interval->y * 12) + $interval->m;
            $maxAllowedPeriode = $months + 1;
        }

        // Ensure minimum periode is 1
        $maxAllowedPeriode = max(1, $maxAllowedPeriode);

        echo "Processing: {$container['nomor_kontainer']} ({$container['vendor']})\n";
        echo "  Start: {$container['tanggal_awal']}\n";
        echo "  End: " . ($container['tanggal_akhir'] ?? 'Ongoing') . "\n";
        echo "  Current periods: {$container['all_periods']}\n";
        echo "  Max allowed periode: {$maxAllowedPeriode}\n";

        // Delete periods that exceed the allowed limit
        $deleteStmt = $pdo->prepare("
            DELETE FROM daftar_tagihan_kontainer_sewa
            WHERE nomor_kontainer = ?
            AND vendor = ?
            AND tanggal_awal = ?
            AND periode > ?
        ");

        $params = [
            $container['nomor_kontainer'],
            $container['vendor'],
            $container['tanggal_awal'],
            $maxAllowedPeriode
        ];

        if ($container['tanggal_akhir']) {
            $deleteStmt = $pdo->prepare("
                DELETE FROM daftar_tagihan_kontainer_sewa
                WHERE nomor_kontainer = ?
                AND vendor = ?
                AND tanggal_awal = ?
                AND (tanggal_akhir = ? OR tanggal_akhir IS NULL)
                AND periode > ?
            ");
            $params = [
                $container['nomor_kontainer'],
                $container['vendor'],
                $container['tanggal_awal'],
                $container['tanggal_akhir'],
                $maxAllowedPeriode
            ];
        }

        $deleteStmt->execute($params);
        $deletedCount = $deleteStmt->rowCount();
        $totalDeleted += $deletedCount;

        if ($deletedCount > 0) {
            echo "  ❌ Deleted {$deletedCount} invalid periods\n";
        } else {
            echo "  ✅ No invalid periods found\n";
        }
        echo "  ---\n";
    }

    echo "\n=== CLEANUP SUMMARY ===\n";
    echo "Containers processed: {$containersProcessed}\n";
    echo "Total periods deleted: {$totalDeleted}\n";

    // Show final distribution
    echo "\n=== FINAL PERIODE DISTRIBUTION ===\n";
    $stmt = $pdo->query("SELECT periode, COUNT(*) as count FROM daftar_tagihan_kontainer_sewa GROUP BY periode ORDER BY periode");
    $finalDistribution = $stmt->fetchAll();

    foreach ($finalDistribution as $dist) {
        echo "Periode {$dist['periode']}: {$dist['count']} records\n";
    }

    // Check if there are still periods > 18 (which would be unusual)
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM daftar_tagihan_kontainer_sewa WHERE periode > 18");
    $highPeriods = $stmt->fetch();

    if ($highPeriods['count'] > 0) {
        echo "\n⚠️  WARNING: {$highPeriods['count']} records still have periode > 18\n";
        echo "These might need manual review.\n";
    } else {
        echo "\n✅ All periods are now within reasonable limits!\n";
    }

} catch(PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . PHP_EOL;
} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
}
