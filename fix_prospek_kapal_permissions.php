<?php

// Load Laravel environment
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get database configuration
$config = config('database.connections.mysql');

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']};charset=utf8",
        $config['username'],
        $config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database successfully.\n";

    // Get admin user ID
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        echo "Admin user not found!\n";
        exit(1);
    }

    $adminId = $admin['id'];
    echo "Found admin user with ID: $adminId\n";

    // Prospek Kapal permissions to add with correct format (using dashes)
    $permissions = [
        'prospek-kapal-view' => 'View Prospek Kapal',
        'prospek-kapal-create' => 'Create Prospek Kapal',
        'prospek-kapal-update' => 'Update Prospek Kapal',
        'prospek-kapal-delete' => 'Delete Prospek Kapal',
        'prospek-kapal-export' => 'Export Prospek Kapal',
        'prospek-kapal-import' => 'Import Prospek Kapal',
        'prospek-kapal-approve' => 'Approve Prospek Kapal',
        'prospek-kapal-report' => 'View Prospek Kapal Report'
    ];

    $addedCount = 0;

    foreach ($permissions as $permission => $description) {
        // First, check if permission exists in permissions table
        $checkPermStmt = $pdo->prepare("SELECT id FROM permissions WHERE name = ?");
        $checkPermStmt->execute([$permission]);
        $permissionRecord = $checkPermStmt->fetch(PDO::FETCH_ASSOC);

        if (!$permissionRecord) {
            // Create permission if it doesn't exist
            $insertPermStmt = $pdo->prepare("INSERT INTO permissions (name, description, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $insertPermStmt->execute([$permission, $description]);
            $permissionId = $pdo->lastInsertId();
            echo "Created new permission: $permission - $description\n";
        } else {
            $permissionId = $permissionRecord['id'];
            echo "Permission already exists: $permission\n";
        }

        // Check if user already has this permission
        $checkUserPermStmt = $pdo->prepare("SELECT COUNT(*) FROM user_permissions WHERE user_id = ? AND permission_id = ?");
        $checkUserPermStmt->execute([$adminId, $permissionId]);
        $exists = $checkUserPermStmt->fetchColumn();

        if ($exists == 0) {
            // Add permission to user
            $insertUserPermStmt = $pdo->prepare("INSERT INTO user_permissions (user_id, permission_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $insertUserPermStmt->execute([$adminId, $permissionId]);
            echo "Added permission to admin: $permission\n";
            $addedCount++;
        } else {
            echo "Admin already has permission: $permission\n";
        }
    }

    echo "\nCompleted! Added $addedCount new permissions to admin user.\n";
    echo "Admin user now has access to Prospek Kapal menu with correct permission format.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
