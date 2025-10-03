<?php
// Check database tables
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=aypsis', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Checking Permission System Tables ===\n\n";

    // Show all tables
    $stmt = $pdo->query("SHOW TABLES");
    $allTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $permissionTables = [];
    foreach ($allTables as $table) {
        if (strpos($table, 'permission') !== false || strpos($table, 'role') !== false) {
            $permissionTables[] = $table;
        }
    }

    echo "Permission and Role Related Tables:\n";
    foreach ($permissionTables as $table) {
        echo "  - $table\n";
    }

    // Now let's test the correct table names
    echo "\n=== Testing Permission Check ===\n";

    // Get admin user
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        echo "❌ Admin user not found\n";
        exit;
    }

    echo "✅ Admin user found (ID: {$admin['id']})\n";

    // Check for user_has_permissions or similar table
    $userPermissionTables = [];
    foreach ($allTables as $table) {
        if (strpos($table, 'user') !== false && strpos($table, 'permission') !== false) {
            $userPermissionTables[] = $table;
        }
    }

    echo "\nUser-Permission tables:\n";
    foreach ($userPermissionTables as $table) {
        echo "  - $table\n";

        // Try to check admin's permissions in this table
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM `$table` WHERE user_id = ?");
            $stmt->execute([$admin['id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "    Admin has {$result['count']} entries in $table\n";
        } catch (Exception $e) {
            echo "    Error accessing $table: " . $e->getMessage() . "\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
