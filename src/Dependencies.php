<?php
$injector = new \Auryn\Injector();

$injector->alias('Http\Request', 'Http\HttpRequest');
$injector->share('Http\HttpRequest');
$injector->define('Http\HttpRequest', [
    ':get' => $_GET,
    ':post' => $_POST,
    ':cookies' => $_COOKIE,
    ':files' => $_FILES,
    ':server' => $_SERVER
]);

$injector->alias('Http\Response', 'Http\HttpResponse');
$injector->share('Http\HttpResponse');

$injector->alias('PHPDatastream\Template\Renderer', 'PHPDatastream\Template\MustacheRenderer');
$injector->define('Mustache_Engine', [
    ':options' => [
        'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/templates', [
            'extension' => '.html'
        ])
    ]
]);

return $injector;
