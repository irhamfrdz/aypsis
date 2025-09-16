<?php

$host = '127.0.0.1';
$dbname = 'aypsis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Master karyawan permissions:\n";
    $stmt = $pdo->query("SELECT id, name FROM permissions WHERE name LIKE 'master-karyawan%' OR name LIKE 'master.karyawan%' ORDER BY name");
    $perms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($perms as $perm) {
        echo "- ID {$perm['id']}: {$perm['name']}\n";
    }

} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}

?>
