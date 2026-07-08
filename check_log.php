<?php

$lines = file('storage/logs/laravel.log');
foreach ($lines as $line) {
    if (strpos($line, 'Update Temas Request:') !== false) {
        $jsonStart = strpos($line, '{');
        if ($jsonStart !== false) {
            $json = substr($line, $jsonStart);
            $data = json_decode($json, true);
            if (isset($data[0])) {
                echo "Section 0:\n";
                foreach ($data[0] as $key => $val) {
                    if (is_array($val)) {
                        echo "  - $key: ".count($val)." items\n";
                    }
                }
            }
        }
    }
}
