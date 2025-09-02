<?php

echo "Testing login and payment access...\n";

// Initialize curl session for login
$ch = curl_init();

// First, get login page to get CSRF token
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
$loginPage = curl_exec($ch);

// Extract CSRF token
preg_match('/<input[^>]*name="_token"[^>]*value="([^"]*)"/', $loginPage, $matches);
$csrfToken = $matches[1] ?? null;

if (!$csrfToken) {
    echo "Could not find CSRF token\n";
    exit;
}

echo "CSRF Token found: " . substr($csrfToken, 0, 10) . "...\n";

// Now login
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    '_token' => $csrfToken,
    'username' => 'admin',
    'password' => 'admin123' // Try common password
]));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$loginResult = curl_exec($ch);
$loginCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Login attempt - HTTP Code: $loginCode\n";

// Now try to access payment page
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/pranota/payment');
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');
$paymentResult = curl_exec($ch);
$paymentCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Payment page access - HTTP Code: $paymentCode\n";

if ($paymentCode == 200 && strpos($paymentResult, 'login') === false) {
    echo "SUCCESS: Payment page accessible!\n";
} else {
    echo "FAILED: Still redirected to login or error\n";
    echo "Response preview: " . substr($paymentResult, 0, 200) . "...\n";
}

curl_close($ch);
@unlink('cookies.txt');
