<?php

$host = '127.0.0.1';
$dbname = 'aypsis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Users Table Structure ===\n";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($columns as $col) {
        echo $col['Field'] . ' (' . $col['Type'] . ")\n";
    }

    echo "\n=== Finding User test4 ===\n";
    // Try different column names
    $possibleColumns = ['name', 'username', 'email'];

    foreach($possibleColumns as $col) {
        try {
            $stmt = $pdo->prepare("SELECT id, $col FROM users WHERE $col = ?");
            $stmt->execute(['test4']);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if($user) {
                echo "Found user with $col = test4:\n";
                echo "ID: " . $user['id'] . "\n";
                echo "$col: " . $user[$col] . "\n";
                break;
            }
        } catch(PDOException $e) {
            // Column doesn't exist, continue
        }
    }

} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
