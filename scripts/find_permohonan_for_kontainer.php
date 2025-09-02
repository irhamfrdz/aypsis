<?php
$pdo=new PDO('mysql:host=127.0.0.1;dbname=aypsis;charset=utf8mb4','root','');
$stmt=$pdo->prepare('SELECT permohonan_id FROM permohonan_kontainers WHERE kontainer_id = ? LIMIT 1');
$stmt->execute([8]);
$pid = $stmt->fetchColumn();
echo $pid ?: 'NO_PID';
