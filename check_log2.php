<?php

$content = file_get_contents('storage/logs/laravel.log');
echo substr($content, -3000);
