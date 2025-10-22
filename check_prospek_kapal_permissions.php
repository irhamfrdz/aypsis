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
    
    // Get admin user permissions
    $stmt = $pdo->prepare("
        SELECT p.name, p.description 
        FROM user_permissions up 
        JOIN permissions p ON up.permission_id = p.id 
        WHERE up.user_id = 1 AND p.name LIKE '%prospek-kapal%'
        ORDER BY p.name
    ");
    $stmt->execute();
    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nAdmin user permissions for Prospek Kapal:\n";
    if (empty($permissions)) {
        echo "No prospek-kapal permissions found!\n";
    } else {
        foreach ($permissions as $perm) {
            echo "- {$perm['name']}: {$perm['description']}\n";
        }
    }
    
    echo "\nTotal prospek-kapal permissions: " . count($permissions) . "\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}