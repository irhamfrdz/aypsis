<?php

$content = file_get_contents('storage/logs/laravel.log');
$jsonStart = strrpos($content, '{"0":{"kapal":');
if ($jsonStart !== false) {
    $jsonEnd = strpos($content, '}]}', $jsonStart);
    if ($jsonEnd !== false) {
        $json = substr($content, $jsonStart, $jsonEnd - $jsonStart + 3);
        $data = json_decode($json, true);
        if ($data && isset($data['0'])) {
            echo "Parsed JSON successfully.\n";
            $sec = $data['0'];
            foreach (['types', 'manual_names', 'nomor_kontainers', 'custom_prices', 'quantities', 'is_muat', 'is_bongkar'] as $k) {
                if (isset($sec[$k]) && is_array($sec[$k])) {
                    echo "$k: ".count($sec[$k])." items\n";
                }
            }

            // let's print the last 10 elements of custom_prices and nomor_kontainers
            $n = count($sec['types']);
            for ($i = $n - 10; $i < $n; $i++) {
                echo "Item $i: type={$sec['types'][$i]}, cont={$sec['nomor_kontainers'][$i]}, price={$sec['custom_prices'][$i]}\n";
            }
        } else {
            echo "Failed to parse JSON.\n";
        }
    }
}
