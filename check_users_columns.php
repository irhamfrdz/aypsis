<?php

$pdo = new PDO('mysql:host=localhost;dbname=aypsis', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query('SHOW COLUMNS FROM users');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo 'Users table columns:' . PHP_EOL;
foreach ($columns as $col) {
    echo '- ' . $col['Field'] . PHP_EOL;
}
