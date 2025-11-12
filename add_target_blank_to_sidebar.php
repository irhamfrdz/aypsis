<?php

$file = 'resources/views/layouts/app.blade.php';
$content = file_get_contents($file);

// Pattern untuk mencari tag <a href="{{ route(...) }}" yang TIDAK memiliki target="_blank"
$pattern = '/<a href="\{\{ route\([^>]+\)\s*\}\}"(\s+)class="/';
$replacement = '<a href="{{ route$1}}" target="_blank"$1class="';

$newContent = preg_replace($pattern, $replacement, $content);

// Hitung berapa banyak replacement
preg_match_all($pattern, $content, $matches);
$count = count($matches[0]);

if ($count > 0) {
    file_put_contents($file, $newContent);
    echo "Berhasil menambahkan target=\"_blank\" pada $count link di sidebar.\n";
} else {
    echo "Tidak ada link yang perlu diubah (mungkin sudah semua punya target=\"_blank\").\n";
}

echo "\nSelesai!\n";
