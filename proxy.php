<?php

/**
 * Proxy API untuk mengatasi CORS.
 * File ini di-host di domain yang sama dengan frontend (inventoryvendor.pms.web.id)
 * dan meneruskan request ke API backend (inventory.pms.web.id).
 *
 * Penggunaan:
 *   proxy.php?endpoint=ApiStokNol&filter=fast
 *   proxy.php?endpoint=ApiSync/vendorLogin
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Vendor-Token');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$endpoint = $_GET['endpoint'] ?? '';

if (empty($endpoint)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Parameter endpoint wajib diisi']);
    exit;
}

// Tentukan base URL backend
$backendHost = 'https://inventory.pms.web.id';
$targetUrl = $backendHost . '/' . ltrim($endpoint, '/');

// Forward query string (kecuali 'endpoint')
$queryParams = $_GET;
unset($queryParams['endpoint']);
if (!empty($queryParams)) {
    $targetUrl .= '?' . http_build_query($queryParams);
}

// Inisialisasi cURL
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $targetUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => false,
]);

// Forward method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
}

// Forward headers
$forwardHeaders = [];
if (!empty($_SERVER['HTTP_X_VENDOR_TOKEN'])) {
    $forwardHeaders[] = 'X-Vendor-Token: ' . $_SERVER['HTTP_X_VENDOR_TOKEN'];
}
if (!empty($_SERVER['CONTENT_TYPE'])) {
    $forwardHeaders[] = 'Content-Type: ' . $_SERVER['CONTENT_TYPE'];
}
if (!empty($forwardHeaders)) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, $forwardHeaders);
}

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpCode);
header('Content-Type: application/json');
echo $response;
