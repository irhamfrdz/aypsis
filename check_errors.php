<?php
$content = file_get_contents('storage/logs/laravel.log');
preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] local\.ERROR: (.*)/', $content, $matches);
if (!empty($matches[1])) {
    echo implode("\n", array_slice($matches[1], -5));
}
