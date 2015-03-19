<?php
	ini_set('soap.wsdl_cache_enabled', 0);
	ini_set('session.auto_start', 0); 
	ini_set('always_populate_raw_post_data', 1);

	include("system-db.php");
	
	start_db();
	
	echo "<p>TEST 1</p>";
	 $options = array( 
                'soap_version'=>SOAP_1_1, 
                'exceptions'=>true, 
                'trace'=>1, 
                'cache_wsdl'=>WSDL_CACHE_NONE 
            ); 
            
	 $client = new SoapClient('http://services.u-p-n.co.uk/ExternalLinks',$options); 
	 $ns = 'http://tempuri.org';
	$header = new SoapHeader($ns,'TargetVolume','tem');
	$client->__setHeaders($header);
	var_dump($client->__getFunctions()); 
			
	print_r($client->__getLastRequest());
	try {
	echo "<p>TEST 2</p>";
	
$data = <<<XML
	<?xml version="1.0"?>
	<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
   <soapenv:Header/>
   <soapenv:Body>
      <tem:GetNetworkInput>
         <!--Optional:-->
         <tem:Depot>91</tem:Depot>
         <!--Optional:-->
         <tem:JobDate>2015-03-02</tem:JobDate>
         <!--Optional:-->
         <tem:Username>ProdigyWorks!</tem:Username>
         <!--Optional:-->
         <tem:Password>n782723h47h</tem:Password>
      </tem:GetNetworkInput>
   </soapenv:Body>
</soapenv:Envelope>
XML;

	$params = array(
			"Username" => "ProdigyWorks!",
			"Password" => "n782723h47h",
			"Depot" => "91",
			"JobDate" => "2015-03-02"
		);
		
			
	$response = $client->__soapCall("GetNetworkInput",  array('parameters' => $params), array("location" => "http://services.u-p-n.co.uk/ExternalLinks", "uri" => "tem", "soapaction" => "http://tempuri.org/IExternalLinks/GetNetworkInput"));

    echo "====== REQUEST HEADERS =====" . PHP_EOL;
    var_dump($client->__getLastRequestHeaders());
    echo "========= REQUEST ==========" . PHP_EOL;
    var_dump($client->__getLastRequest());
    echo "========= RESPONSE =========" . PHP_EOL;
    var_dump($response);
			echo "<br>RES [" . $response . "]";
		
	
	} catch (Exception $e) {
		    echo "====== ERR REQUEST HEADERS =====" . PHP_EOL;
    var_dump($client->__getLastRequestHeaders());
    echo "========= ERR REQUEST ==========" . PHP_EOL;
    var_dump($client->__getLastRequest());
    echo "========= ERR RESPONSE =========" . PHP_EOL;
		echo "XX:" . $e->getMessage();
	}
?>