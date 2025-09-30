<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/pranota-cat/generate-nomor');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
$response = curl_exec($ch);
curl_close($ch);
echo $response;
