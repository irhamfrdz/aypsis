<?php
$f = "C:/folder_joki/PWA-main/backend/app/Http/Controllers/AmprahanController.php";
$c = file_get_contents($f);
$c = preg_replace("/SELECT p\.id,\s+p\.user_id,\s+p\.status,\s+p\.keterangan_umum,\s+p\.created_at\s*FROM\s+permohonan_amprahans\s+p\s*WHERE/s", "SELECT p.id, p.user_id, p.status, p.keterangan_umum, p.created_at, \n                       u.username as user_nama\n                FROM permohonan_amprahans p\n                LEFT JOIN users u ON p.user_id = u.id\n                WHERE", $c);
file_put_contents($f, $c);
echo "Undone!\n";
