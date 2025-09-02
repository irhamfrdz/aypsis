<?php

echo "Checking authentication status...\n";

// Check if session is working by trying to access login page
$loginUrl = 'http://127.0.0.1:8000/login';
$paymentUrl = 'http://127.0.0.1:8000/pranota/payment';

echo "Login URL: $loginUrl\n";
echo "Payment URL: $paymentUrl\n";

// Simple curl check
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $paymentUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Response Code: $httpCode\n";
if (strpos($response, 'login') !== false) {
    echo "Redirected to login - authentication required\n";
} else {
    echo "Response preview: " . substr($response, 0, 200) . "...\n";
}
