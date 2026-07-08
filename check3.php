<?php

$content = file_get_contents('storage/logs/laravel.log');
$lines = explode("\n", $content);
foreach ($lines as $line) {
    if (strpos($line, 'Update Temas Request: ') !== false) {
        $json = substr($line, strpos($line, 'Update Temas Request: ') + 22);
        $data = json_decode($json, true);
        if ($data) {
            foreach ($data as $sectionIndex => $section) {
                echo "Section $sectionIndex:\n";
                foreach ($section as $key => $val) {
                    if (is_array($val)) {
                        echo "  $key: ".count($val)." items\n";
                    }
                }
            }
        }
    }
}
