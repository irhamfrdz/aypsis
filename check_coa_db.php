<?php
$conn = new mysqli('localhost', 'root', '', 'aypsis');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$result = $conn->query('SELECT name FROM permissions WHERE name LIKE "%coa%"');
echo 'COA Permissions in database:' . PHP_EOL;
while($row = $result->fetch_assoc()) {
    echo '- ' . $row['name'] . PHP_EOL;
}
$conn->close();
?>
