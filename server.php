<?php
/**
 * Emulate Apache's "mod_rewrite" functionality from the
 * built-in PHP web server (thanks to Laravel).
 * 
 * Run following command from root project directory:
 * $ php -S localhost:8000 server.php
 */
$publicDir = __DIR__ . '/htdocs';

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri !== '/' && file_exists($publicDir . '/' . $uri)) {
    return false;
}

require_once $publicDir . '/index.php';