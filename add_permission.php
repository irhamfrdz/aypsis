<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=aypsis', 'root', '');

    // Check table structure first
    $stmt = $pdo->prepare("DESCRIBE permissions");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Permissions table structure:" . PHP_EOL;
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")" . PHP_EOL;
    }
    echo PHP_EOL;

    // Check if permission exists
    $stmt = $pdo->prepare("SELECT * FROM permissions WHERE name = 'approval-dashboard'");
    $stmt->execute();
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        echo "Permission 'approval-dashboard' already exists with ID: " . $existing['id'] . PHP_EOL;
        $permissionId = $existing['id'];
    } else {
        // Add permission (without guard_name if column doesn't exist)
        $stmt = $pdo->prepare("INSERT INTO permissions (name, created_at, updated_at) VALUES ('approval-dashboard', NOW(), NOW())");
        $stmt->execute();
        $permissionId = $pdo->lastInsertId();
        echo "Permission 'approval-dashboard' added with ID: " . $permissionId . PHP_EOL;
    }

    // Check what permission-related tables exist
    $stmt = $pdo->prepare("SHOW TABLES LIKE '%permission%'");
    $stmt->execute();
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Permission-related tables:" . PHP_EOL;
    foreach ($tables as $table) {
        echo "- " . $table . PHP_EOL;
    }
    echo PHP_EOL;

    // Assign to admin role using permission_role table
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'admin' LIMIT 1");
    $stmt->execute();
    $adminRoleId = $stmt->fetchColumn();

    if ($adminRoleId) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO permission_role (permission_id, role_id) VALUES (?, ?)");
        $stmt->execute([$permissionId, $adminRoleId]);
        echo "Permission assigned to admin role (ID: $adminRoleId)" . PHP_EOL;
    }

    // Assign to user ID 1 using user_permissions table
    $stmt = $pdo->prepare("INSERT IGNORE INTO user_permissions (user_id, permission_id) VALUES (1, ?)");
    $stmt->execute([$permissionId]);
    echo "Permission assigned to user ID 1" . PHP_EOL;

    echo "✅ Permission setup completed!" . PHP_EOL;

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>
