<?php

/**
 * env.js - Menyediakan konfigurasi API URL untuk JavaScript.
 * Membaca URL dari .env berdasarkan APP_ENV.
 */
header('Content-Type: application/javascript');

require_once __DIR__ . '/config.php';

$url = APP_ENV === 'developmentd' ? DEV_API_URL : API_BASE_URL;
?>
var API_BASE_URL = '<?= $url ?>';