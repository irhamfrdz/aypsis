<?php

try {
    // Database configuration from .env file
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
        echo "User test4 not found!\n";
        exit(1);
    }

    echo "Found user: {$user['username']} (ID: {$user['id']})\n";

    // Check current permissions for test4
    $stmt = $pdo->prepare("
        SELECT p.name, up.user_id, up.permission_id
        FROM user_permissions up
        JOIN permissions p ON up.permission_id = p.id
        WHERE up.user_id = ?
    ");
    $stmt->execute([$user['id']]);
    $currentPermissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\nCurrent permissions for test4:\n";
    foreach ($currentPermissions as $perm) {
        echo "- {$perm['name']} (Permission ID: {$perm['permission_id']})\n";
    }

    // Find the master-karyawan permission
    $stmt = $pdo->prepare("SELECT id, name FROM permissions WHERE name = ?");
    $stmt->execute(['master-karyawan']);
    $masterKaryawanPermission = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$masterKaryawanPermission) {
        echo "\nPermission 'master-karyawan' not found in permissions table!\n";
        exit(1);
    }

    echo "\nFound master-karyawan permission: {$masterKaryawanPermission['name']} (ID: {$masterKaryawanPermission['id']})\n";

    // Check if user already has master-karyawan permission
    $stmt = $pdo->prepare("SELECT user_id FROM user_permissions WHERE user_id = ? AND permission_id = ?");
    $stmt->execute([$user['id'], $masterKaryawanPermission['id']]);
    $existingPermission = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingPermission) {
        echo "\nUser test4 already has master-karyawan permission.\n";
        echo "No action needed.\n";
    } else {
        echo "\nUser test4 does NOT have master-karyawan permission.\n";
        echo "Adding master-karyawan permission to test4...\n";

        // Add the permission
        $stmt = $pdo->prepare("INSERT INTO user_permissions (user_id, permission_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $result = $stmt->execute([$user['id'], $masterKaryawanPermission['id']]);

        if ($result) {
            echo "✅ Successfully added master-karyawan permission to test4!\n";
        } else {
            echo "❌ Failed to add master-karyawan permission to test4!\n";
        }
    }

    // Verify the final permissions
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

    // Check specifically for karyawan-related permissions
    echo "\n=== KARYAWAN-RELATED PERMISSIONS ===\n";
    $karyawanPermissions = array_filter($finalPermissions, function($perm) {
        return strpos($perm['name'], 'karyawan') !== false;
    });

    if (count($karyawanPermissions) > 0) {
        echo "Karyawan permissions found:\n";
        foreach ($karyawanPermissions as $perm) {
            echo "- {$perm['name']}\n";
        }
    } else {
        echo "No karyawan permissions found!\n";
    }

    echo "\n=== SIDEBAR CHECK ===\n";
    $hasMasterKaryawan = in_array('master-karyawan', array_column($finalPermissions, 'name'));
    $hasKaryawanView = in_array('master-karyawan.view', array_column($finalPermissions, 'name'));

    echo "Has master-karyawan permission: " . ($hasMasterKaryawan ? "✅ YES" : "❌ NO") . "\n";
    echo "Has master-karyawan.view permission: " . ($hasKaryawanView ? "✅ YES" : "❌ NO") . "\n";

    if ($hasMasterKaryawan) {
        echo "\n🎉 SUCCESS: User test4 should now see the Karyawan menu in the sidebar!\n";
    } else {
        echo "\n❌ ISSUE: User test4 still doesn't have the required master-karyawan permission for sidebar display.\n";
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
