<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);


//POST /aws_test/aws.asmx HTTP/1.1
//Host: www.auroracloud.com.au/

//Content-Length: length
 
$barcode = "923959";
$pin = "8136";
 

$url = "http://www.auroracloud.com.au/aws/aws.asmx";
    
    
$request =  chr(60) . "?xml version=\"1.0\" encoding=\"utf-8\"?>
<soap12:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap12=\"http://www.w3.org/2003/05/soap-envelope\">
  <soap12:Body>
    <UserPrivileges xmlns=\"http://ait.com.au/AWS\">
      <OperatorLanguage>eng</OperatorLanguage>
      <OperatorID>webopac</OperatorID>
      <OperatorPwd>123</OperatorPwd>
      <WorkstationName>EBooks</WorkstationName>
      <InstitutionID>QPIL</InstitutionID>
      <AccessPoint>0</AccessPoint>
      <Identifier>$barcode</Identifier>
      <SigninPassword>$pin</SigninPassword>
      <SigninPIN>$pin</SigninPIN>
      <RunTimeParameters>0</RunTimeParameters>
    </UserPrivileges>
  </soap12:Body>
</soap12:Envelope>";


$opts['http']['method'] = 'POST';
$opts['http']['header'] = "Content-Type: application/soap+xml; charset=utf-8";   
$opts['http']['header'] = 'Content-type: application/x-www-form-urlencoded'; 


$context  = stream_context_create($opts);



$output = file_get_contents($url, false, $context);


//echo "$url";

echo  $output;



?>