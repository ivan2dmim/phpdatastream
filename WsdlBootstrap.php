#!/usr/bin/env php
<?php
/*
 * Generate PHP classes from WSDL definition
 */
$vendorDir = __DIR__ . '/vendor';
$inputFile = 'http://dataworks.thomson.com/Dataworks/Enterprise/1.0/webServiceClient.asmx?WSDL';
$outputDir = $vendorDir . '/Dataworks/Enterprise';
$namespaceName = 'Dataworks\Enterprise';

// make sure composer is up to date
system('composer update', $res);
if ($res != 0) {
    die();
}

require $vendorDir . '/autoload.php';

// clear output directory before gnerating classes
if (is_dir($outputDir)) {
    $files = array_diff(scandir($outputDir), array(
        '.',
        '..'
    ));
    foreach ($files as $fileName) {
        unlink($outputDir . '/' . $fileName);
    }
    rmdir($outputDir);
}

$generator = new \Wsdl2PhpGenerator\Generator();
$generator->generate(new \Wsdl2PhpGenerator\Config(array(
    'inputFile' => $inputFile,
    'outputDir' => $outputDir,
    'namespaceName' => $namespaceName,
    'soapClientOptions' => array(
        'connection_timeout' => 10
    )
)));

// update composer autoloader
system('composer update', $res);
if ($res != 0) {
    die();
}
