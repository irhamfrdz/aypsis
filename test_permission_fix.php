<?php

// Test permission fix - langsung ke database
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=aypsis', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Permission Fix Test ===\n\n";

    // 1. Cek user admin
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        echo "✅ Admin user found (ID: {$admin['id']}, Username: {$admin['username']})\n";
    } else {
        echo "❌ Admin user NOT found\n";
        exit;
    }

    // 2. Cek permission
    $stmt = $pdo->prepare("SELECT id, name FROM permissions WHERE name = 'tagihan-kontainer-sewa-create'");
    $stmt->execute();
    $permission = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($permission) {
        echo "✅ Permission 'tagihan-kontainer-sewa-create' exists (ID: {$permission['id']})\n";
    } else {
        echo "❌ Permission 'tagihan-kontainer-sewa-create' NOT found\n";
        exit;
    }

    // 3. Cek apakah admin punya permission ini via roles
    $stmt = $pdo->prepare("
        SELECT r.name as role_name, p.name as permission_name
        FROM model_has_roles mhr
        JOIN roles r ON r.id = mhr.role_id
        JOIN role_has_permissions rhp ON rhp.role_id = r.id
        JOIN permissions p ON p.id = rhp.permission_id
        WHERE mhr.model_type = 'App\\\\Models\\\\User'
        AND mhr.model_id = ?
        AND p.name = 'tagihan-kontainer-sewa-create'
    ");
    $stmt->execute([$admin['id']]);
    $rolePermissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rolePermissions) > 0) {
        echo "✅ Admin has permission via roles:\n";
        foreach ($rolePermissions as $rp) {
            echo "   - Role: {$rp['role_name']}\n";
        }
    } else {
        echo "❌ Admin does NOT have permission via roles\n";
    }

    echo "\n=== Fix Summary ===\n";
    echo "Blade template updated from @can('tagihan-kontainer-create') to @can('tagihan-kontainer-sewa-create')\n";
    echo "Routes already using correct permission: 'tagihan-kontainer-sewa-create'\n";

    if (count($rolePermissions) > 0) {
        echo "\n🎉 RESULT: Import buttons should now be visible to admin user!\n";
    } else {
        echo "\n⚠️ RESULT: Admin still needs the permission. Adding it now...\n";

        // Add permission to admin role
        $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'admin'");
        $stmt->execute();
        $adminRole = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($adminRole) {
            // Check if permission already exists for admin role
            $stmt = $pdo->prepare("
                SELECT * FROM role_has_permissions
                WHERE role_id = ? AND permission_id = ?
            ");
            $stmt->execute([$adminRole['id'], $permission['id']]);
            $exists = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$exists) {
                $stmt = $pdo->prepare("
                    INSERT INTO role_has_permissions (role_id, permission_id)
                    VALUES (?, ?)
                ");
                $stmt->execute([$adminRole['id'], $permission['id']]);
                echo "✅ Permission added to admin role!\n";
            } else {
                echo "✅ Permission already exists for admin role!\n";
            }
        }
    }

    echo "\n=== Test completed ===\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
