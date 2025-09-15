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

    // Check users table structure
    echo "=== USERS TABLE STRUCTURE ===\n";
    $stmt = $pdo->query('DESCRIBE users');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }

    echo "\n=== PERMISSIONS TABLE STRUCTURE ===\n";
    $stmt = $pdo->query('DESCRIBE permissions');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }

    echo "\n=== USER_PERMISSIONS TABLE STRUCTURE ===\n";
    $stmt = $pdo->query('DESCRIBE user_permissions');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }

    // Check sample data
    echo "\n=== SAMPLE USERS ===\n";
    $stmt = $pdo->query('SELECT id, username, name FROM users LIMIT 5');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']}, Username: {$row['username']}, Name: " . ($row['name'] ?? 'NULL') . "\n";
    }

    echo "\n=== SAMPLE PERMISSIONS ===\n";
    $stmt = $pdo->query('SELECT id, name FROM permissions WHERE name LIKE "%karyawan%" LIMIT 10');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']}, Name: {$row['name']}\n";
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}

?>
