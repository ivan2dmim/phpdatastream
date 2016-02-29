<?php
namespace PHPDatastream;

use Noodlehaus\Config;
require __DIR__ . '/../vendor/autoload.php';

$debug = false;
$useXML = true;
$echoData = true;

$confDir = __DIR__ . '/../conf';
$conf = new Config([
    $confDir . '/config.dist.ini',
    '?' . $confDir . '/config.ini'
]);
$userData = $conf->get('UserData');

$user = new \Dataworks\Enterprise\UserData();
$user->setUsername($userData['username']);
$user->setPassword($userData['password']);
$user->setRealm($userData['realm']);

$service = new \Dataworks\Enterprise\WebServiceClient();
$request = new \Dataworks\Enterprise\RequestData();

/*
 * $parameters = new DataworksEnterprise\Sources($user, 0);
 * $response = $service->Sources($parameters);
 * var_dump($response->getSourcesResult());die;
 * die;
 */
$request->setSource('Datastream');
// $request->setSource('XREF');

/*
 * $fields = new Dataworks\Enterprise\ArrayOfString();
 * $fields->setString(array('CCY_3'));
 * $request->setFields($fields);
 */

// $request->setInstrument('SWISSMI,ASX200I~=PI,RI~-5D');
// $request->setInstrument('SWISSMI~LIST');
// $request->setInstrument('SWISSMI,ASX200I~=PI,PO,PH,PL~-5D');
// /result.php?type=TS&format=HRC$&na=NA&series=CH0185709083&startdate=2016-01-19&datatype=X(P),X(P)~CHF,IF#(X(P#S),NA,ZERO),IF#(NA#(MAX#(X(P#S))),NE,ZERO)&output=text HTTP/1.1
// $request->setInstrument('CH0185709083~=X(P),X(P)~~CHF,IF#(X(P#S),NA,ZERO),IF#(NA#(MAX#(X(P#S))),NE,ZERO)~-5D');
// LU0128497707","X(P),X(P)~CHF,IF#(X(P#S),NA,ZERO),IF#(NA#(MAX#(X(P#S))),NE,ZERO)
// $request->setInstrument('LU0128497707(P),LU0128497707(P)~~CHF,IF#(XU0128497707(P#S),NA,ZERO),IF#(NA#(MAX#(LU0128497707(P#S))),NE,ZERO)~-5D');
$series = 'SWISSMI,ASX200';
$series = 'DS1,DS2,MAX#(DS4,1),DS4';
$dataTypes = 'IF#(X(P#S),NA,ZERO),X(P),X(P)~CHF,IF#(NA#(MAX#(X(P#S))),NE,ZERO),X';

$series = '1,2,MAX#(3,1),4';
$dataTypes = 'X(A),X(B),X';

$series = '1,2';
$dataTypes = 'X(A),X(B)';

$series = 'LU0128497707';
// $series = 'SWISSMI,ASX200';
$dataTypes = 'X(P),X(P)~CHF,IF#(X(P#S),NA,ZERO),IF#(NA#(MAX#(X(P#S))),NE,ZERO)';
// $dataTypes = 'X(P),X(P)~CHF,IF#(X(P#S),NA,ZERO),IF#(NA#(MAX#(X(P#S))),NE,ZERO)';

$startDate = '';
$endDate = '';
$frequency = '';
$naValue = '';

// $series = 'JAPDOWA(PI),DUMMY,ASX200I(RI),ASX200I(PI)';
// $dataTypes = '';
// $series = 'ASX200I';
// $dataTypes = 'RI';
$series = 'MSWRLD$(PI),SWISSMI(PI),ASX200I(PI)';
$dataTypes = '';
// $startDate = '2016-02-13';
// $endDate = '2016-02-23';
$startDate = '-5D';
$frequency = 'D';
$naValue = 'NaN';

$builder = new ExpressionBuilder($series, $dataTypes, $startDate, $endDate, $frequency, $naValue);
$request->setInstrument($builder->getInstrument());
// var_dump($builder->getInstrument(),$builder->getSymbols());die;
// $request->setSymbolSet($SymbolSet)

try {
    if ($useXML) {
        if ($debug) {
            $xmlFileName = 'PI#S-as-datatype.xml';
            $xmlFileName = 'PI#S-as-instrument.xml';
            $xmlDir = __DIR__ . '/../tests/fixtures/xml';
            
            $xmlContent = file_get_contents($xmlDir . '/' . $xmlFileName);
            $record = new \Dataworks\Enterprise\RequestRecordAsXmlResult($xmlContent);
        } else {
            $parameters = new \Dataworks\Enterprise\RequestRecordAsXml($user, $request, 2);
            $response = $service->RequestRecordAsXml($parameters);
            $record = $response->getRequestRecordAsXmlResult();
        }
        
        $xmlRecord = new \SimpleXMLElement($record->getAny());
        
        if ($xmlRecord->StatusType->__toString() == \Dataworks\Enterprise\StatusType::Connected) {
            // process results
            if ($echoData) {
                echo $record->getAny();
            }
        } else {
            echo $xmlRecord->StatusMessage->__toString();
        }
    } else {
        $parameters = new \Dataworks\Enterprise\RequestRecord($user, $request, 0);
        $response = $service->RequestRecord($parameters);
        $record = $response->getRequestRecordResult();
        if ($record->getStatusType() == \DataworksEnterprise\StatusType::Connected) {
            $field = new \Dataworks\Enterprise\Field();
            
            foreach ($record->getFields() as $field) {
                $x = $field->getName();
                $y = $field->getValue();
                $z = $field->getArrayValue();
                var_dump($x, $y, $z);
            }
        } else {
            print $record->getStatusMessage();
        }
    }
} catch (\Exception\SoapFault $e) {
    var_dump($e);
    echo $e->getMessage();
} catch (\Exception $e) {
    var_dump($e);
    echo $e->getMessage();
}