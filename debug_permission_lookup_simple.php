<?php

$host = '127.0.0.1';
$dbname = 'aypsis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Testing Permission Lookup ===\n";

    // Test direct lookup
    $testCases = [
        'master-karyawan.view',
        'master-karyawan-view',
        'master.karyawan.view',
        'master.karyawan.show'
    ];

    foreach($testCases as $testCase) {
        $stmt = $pdo->prepare("SELECT id, name FROM permissions WHERE name = ?");
        $stmt->execute([$testCase]);
        $perm = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($perm) {
            echo "✓ Found: {$testCase} -> ID {$perm['id']}\n";
        } else {
            echo "✗ Not found: {$testCase}\n";
        }
    }

    echo "\n=== Simulating convertMatrixPermissionsToIds Logic ===\n";

    // Simulate the logic for master-karyawan with view action
    $module = 'master-karyawan';
    $action = 'view';
    $dbAction = 'view'; // from actionMap

    echo "Input: module='$module', action='$action', dbAction='$dbAction'\n";

    // Test PATCH logic
    $permissionName1 = $module . '.' . $dbAction; // master-karyawan.view
    echo "Checking: $permissionName1\n";

    $stmt = $pdo->prepare("SELECT id, name FROM permissions WHERE name = ?");
    $stmt->execute([$permissionName1]);
    $perm1 = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($perm1) {
        echo "✓ PATCH found: {$perm1['name']} (ID {$perm1['id']})\n";
    } else {
        echo "✗ PATCH not found: $permissionName1\n";

        // Test fallback
        $moduleParts = explode('-', $module);
        $baseModule = $moduleParts[0]; // master
        $subModule = $moduleParts[1]; // karyawan
        $permissionName2 = $baseModule . '.' . $subModule . '.' . $dbAction; // master.karyawan.view
        echo "Checking fallback: $permissionName2\n";

        $stmt->execute([$permissionName2]);
        $perm2 = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($perm2) {
            echo "✓ Fallback found: {$perm2['name']} (ID {$perm2['id']})\n";
        } else {
            echo "✗ Fallback not found: $permissionName2\n";
        }
    }

} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}

?>
