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
    
    // Check users table structure first
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Available columns in users table: " . implode(', ', $columns) . "\n";
    
    // Get admin user ID - try different possible column names
    $whereClause = in_array('email', $columns) ? "username = 'admin' OR email = 'admin@admin.com'" : "username = 'admin'";
    $stmt = $pdo->prepare("SELECT id FROM users WHERE $whereClause LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin) {
        echo "Admin user not found!\n";
        exit(1);
    }
    
    $adminId = $admin['id'];
    echo "Found admin user with ID: $adminId\n";
    
    // Check user_permissions table structure
    $stmt = $pdo->query("DESCRIBE user_permissions");
    $permColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Available columns in user_permissions table: " . implode(', ', $permColumns) . "\n";
    
    // Check permissions table structure
    $stmt = $pdo->query("DESCRIBE permissions");
    $permissionsColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Available columns in permissions table: " . implode(', ', $permissionsColumns) . "\n";
    
    // Prospek Kapal permissions to add
    $permissions = [
        'prospek_kapal.index' => 'View Prospek Kapal List',
        'prospek_kapal.create' => 'Create Prospek Kapal',
        'prospek_kapal.store' => 'Store Prospek Kapal',
        'prospek_kapal.show' => 'Show Prospek Kapal Detail',
        'prospek_kapal.edit' => 'Edit Prospek Kapal',
        'prospek_kapal.update' => 'Update Prospek Kapal',
        'prospek_kapal.destroy' => 'Delete Prospek Kapal',
        'prospek_kapal.export' => 'Export Prospek Kapal',
        'prospek_kapal.import' => 'Import Prospek Kapal',
        'prospek_kapal.approve' => 'Approve Prospek Kapal',
        'prospek_kapal.reject' => 'Reject Prospek Kapal',
        'prospek_kapal.report' => 'View Prospek Kapal Report'
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
    echo "Admin user now has access to all Prospek Kapal functionality.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}