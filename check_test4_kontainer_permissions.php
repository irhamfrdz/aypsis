<?php

$host = '127.0.0.1';
$dbname = 'aypsis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check user test4 master-kontainer permissions
    $stmt = $pdo->prepare("
        SELECT p.name
        FROM permissions p
        INNER JOIN user_permissions up ON p.id = up.permission_id
        WHERE up.user_id = ? AND p.name LIKE 'master-kontainer%'
        ORDER BY p.name
    ");
    $stmt->execute([10]); // User ID 10 for test4
    $perms = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "User test4 master-kontainer permissions:\n";
    foreach($perms as $perm) {
        echo "- {$perm}\n";
    }

    if (empty($perms)) {
        echo "No master-kontainer permissions found for user test4\n";
    }

} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}

?>
