<?php

/**
 * Config - membaca .env dan menyediakan konstanta API.
 */
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('API_BASE_URL', getenv('PROD_API_URL') ?: 'https://inventory.pms.web.id');
define('DEV_API_URL', getenv('DEV_API_URL') ?: 'http://localhost/inventory/public');
