#!/usr/bin/env php
<?php
system('composer update', $res);
if ($res != 0) {
    die();
}

require __DIR__ . '/vendor/autoload.php';

$inputFile = 'http://dataworks.thomson.com/Dataworks/Enterprise/1.0/webServiceClient.asmx?WSDL';
$outputDir = __DIR__ . '/vendor/Dataworks/Enterprise';
// $namespaceName = 'PHPDatastream\Dataworks\Enterprise';
$namespaceName = 'Dataworks\Enterprise';

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
// die();

$generator = new \Wsdl2PhpGenerator\Generator();
$generator->generate(new \Wsdl2PhpGenerator\Config(array(
    'inputFile' => $inputFile,
    'outputDir' => $outputDir,
    'namespaceName' => $namespaceName,
    'soapClientOptions' => array(
        'connection_timeout' => 10
    )
)));

system('composer update', $res);
if ($res != 0) {
    die();
}
