<?php

try {
    // Database configuration
    $dbConfig = [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'aypsis',
        'username' => 'root',
        'password' => ''
    ];

    // Connect to database
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']}",
        $dbConfig['username'],
        $dbConfig['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Connected to database successfully!\n\n";

    // Find user test4
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE username = ?");
    $stmt->execute(['test4']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "❌ User test4 not found!\n";
        exit(1);
    }

    echo "Found user: {$user['username']} (ID: {$user['id']})\n\n";

    // Check current permissions for test4
    $stmt = $pdo->prepare("
        SELECT p.name, up.user_id, up.permission_id
        FROM user_permissions up
        JOIN permissions p ON up.permission_id = p.id
        WHERE up.user_id = ?
    ");
    $stmt->execute([$user['id']]);
    $currentPermissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Current permissions for test4:\n";
    foreach ($currentPermissions as $perm) {
        echo "- {$perm['name']} (Permission ID: {$perm['permission_id']})\n";
    }
    echo "\n";

    // Check if user has master-karyawan permission
    $stmt = $pdo->prepare("SELECT user_id FROM user_permissions WHERE user_id = ? AND permission_id = (SELECT id FROM permissions WHERE name = ?)");
    $stmt->execute([$user['id'], 'master-karyawan']);
    $existingPermission = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingPermission) {
        echo "⚠️ User test4 has master-karyawan permission that is no longer needed.\n";
        echo "Removing master-karyawan permission since sidebar now uses master-karyawan-view only...\n\n";

        // Remove the master-karyawan permission
        $stmt = $pdo->prepare("DELETE FROM user_permissions WHERE user_id = ? AND permission_id = (SELECT id FROM permissions WHERE name = ?)");
        $result = $stmt->execute([$user['id'], 'master-karyawan']);

        if ($result) {
            echo "✅ Successfully removed master-karyawan permission from test4!\n";
        } else {
            echo "❌ Failed to remove master-karyawan permission from test4!\n";
        }
    } else {
        echo "ℹ️ User test4 does not have master-karyawan permission. No action needed.\n";
    }

    // Verify final permissions
    echo "\n=== FINAL VERIFICATION ===\n";
    $stmt = $pdo->prepare("
        SELECT p.name
        FROM user_permissions up
        JOIN permissions p ON up.permission_id = p.id
        WHERE up.user_id = ?
        ORDER BY p.name
    ");
    $stmt->execute([$user['id']]);
    $finalPermissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Final permissions for test4:\n";
    foreach ($finalPermissions as $perm) {
        echo "- {$perm['name']}\n";
    }

    // Check karyawan-related permissions
    echo "\n=== KARYAWAN PERMISSIONS CHECK ===\n";
    $karyawanPermissions = array_filter($finalPermissions, function($perm) {
        return strpos($perm['name'], 'karyawan') !== false;
    });

    if (count($karyawanPermissions) > 0) {
        echo "Karyawan permissions found:\n";
        foreach ($karyawanPermissions as $perm) {
            echo "- {$perm['name']}\n";
        }
    } else {
        echo "❌ No karyawan permissions found!\n";
    }

    // Check sidebar visibility
    $hasMasterKaryawanView = in_array('master-karyawan-view', array_column($finalPermissions, 'name'));
    $hasMasterKaryawan = in_array('master-karyawan', array_column($finalPermissions, 'name'));

    echo "\n=== SIDEBAR VISIBILITY CHECK ===\n";
    echo "Has master-karyawan-view: " . ($hasMasterKaryawanView ? "✅ YES" : "❌ NO") . "\n";
    echo "Has master-karyawan: " . ($hasMasterKaryawan ? "⚠️ YES (no longer needed)" : "✅ NO (correct)") . "\n";

    if ($hasMasterKaryawanView) {
        echo "\n🎉 SUCCESS: User test4 has master-karyawan-view permission.\n";
        echo "   The Karyawan menu SHOULD appear in the sidebar.\n";
        echo "   Sidebar logic updated to use: @if(\$user && \$user->can('master-karyawan-view'))\n";
    } else {
        echo "\n❌ ISSUE: User test4 is missing master-karyawan-view permission.\n";
        echo "   The Karyawan menu will NOT appear in the sidebar.\n";
    }

} catch (Exception $e) {
    echo '❌ Database error: ' . $e->getMessage() . "\n";
}

?>
