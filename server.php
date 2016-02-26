<?php
/**
 * This file allows us to emulate Apache's "mod_rewrite" functionality from the
 * built-in PHP web server. This provides a convenient way to test a Laravel
 * application without having installed a "real" web server software here.
 * 
 * Run following command from root project directory:
 * $ php -S localhost:8000 server.php
 */
// 
// 
// 


$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);
$publicDir = __DIR__ . '/htdocs';
if ($uri !== '/' && file_exists($publicDir . $uri)) {
    return false;
}
require_once $publicDir . '/index.php';