<?php
	ini_set('soap.wsdl_cache_enabled', 0);
	ini_set('session.auto_start', 0); 
	ini_set('always_populate_raw_post_data', 1);

	include("system-db.php");
	include("XML2Array.php");
	
	start_db();
    
	$options = array( 
			'soap_version' => SOAP_1_1, 
			'exceptions' => false, 
			'trace' => 1, 
			'cache_wsdl' => WSDL_CACHE_NONE 
		); 
            
	$client = new SoapClient('http://services.u-p-n.co.uk/ExternalLinks', $options); 

	try {
		$params = array(
				"Username" => "ProdigyWorks!",
				"Password" => "n782723h47h",
				"Depot" => "91",
				"JobDate" => convertStringToDate($_POST['upndate'])
			);
		
		$response = $client->__soapCall(
				"GetNetworkInput",  
				array('parameters' => $params), 
				array(
						"location" => "http://services.u-p-n.co.uk/ExternalLinks", 
						"uri" => "tem", 
						"soapaction" => "http://tempuri.org/IExternalLinks/GetNetworkInput"
					)
			);
	
	    $start = strpos($client->__getLastResponse(), "<s:Envelope");
	    $end = strpos($client->__getLastResponse(), "</s:Envelope");
	    $xml = substr($client->__getLastResponse(), $start, ($end - $start) + 13);
	    
	    $array = XML2Array::createArray($xml);

	    /** open raw memory as file, no need for temp files */
	    $temp_memory = fopen('php://memory', 'w');
	    
    	$line = array();
		
		array_push($line, "Con Barcode");
		array_push($line, "Con Number");
		array_push($line, "Con Signor");
		array_push($line, "Customer Paperwork");
		array_push($line, "Customer Reference");
		array_push($line, "Delivery Date Time");
		array_push($line, "Despatch Date");
		array_push($line, "Depot");
		array_push($line, "Delivery Company Name");
		array_push($line, "Delivery Contact Name");
		array_push($line, "Delivery Address 1");
		array_push($line, "Delivery Address 2");
		array_push($line, "Delivery Town");
		array_push($line, "Delivery County");
		array_push($line, "Delivery Country");
		array_push($line, "Delivery Post Code");
		array_push($line, "Delivery Phone");
		array_push($line, "Extra Service");
		array_push($line, "Main Service");
		array_push($line, "Premium Service");
		array_push($line, "Special Instructions");
		array_push($line, "Tail Lift");
		array_push($line, "Total Weight");
		
		for ($pindex = 1; $pindex <= 10; $pindex++) {
			array_push($line, "Con Bar Code $pindex");
			array_push($line, "Pallet Size $pindex");
			array_push($line, "Pallet Type $pindex");
			array_push($line, "Pallet Barcode $pindex");
		}
		
        fputcsv($temp_memory, $line, ",");
        
		foreach ($array['s:Envelope']['s:Body']['GetNetworkInputResponse']['GetNetworkInputResult']['a:NetworkConsignment'] as $k => $v) {
//			var_dump($v['a:Pallets']);
			
			$line = array();
			
			array_push($line, $v['a:ConBarcode']);
			array_push($line, $v['a:ConNo']);
			array_push($line, $v['a:Consignor']);
			array_push($line, $v['a:CustPaperwork']);
			array_push($line, $v['a:CustRef']);
			array_push($line, $v['a:DeliveryDateTime']);
			array_push($line, $v['a:Despatchdate']);
			array_push($line, $v['a:Depot']);
			array_push($line, $v['a:DeliveryCoName']);
			array_push($line, $v['a:DeliveryContactName']);
			array_push($line, $v['a:DeliveryAdd1']);
			array_push($line, $v['a:DeliveryAdd2']);
			array_push($line, $v['a:DeliveryTown']);
			array_push($line, $v['a:DeliveryCounty']);
			array_push($line, $v['a:DeliveryCountry']);
			array_push($line, $v['a:DeliveryPostcode']);
			array_push($line, $v['a:DeliveryPhone']);
			array_push($line, $v['a:ExtraService']);
			array_push($line, $v['a:MainService']);
			array_push($line, $v['a:PremiumService']);
			array_push($line, $v['a:SpecialInstructions']);
			array_push($line, $v['a:TailLift']);
			array_push($line, $v['a:TotalWeight']);
			array_push($line, $v['a:MainService']);
			
			$pindex = 1;
			
			foreach ($v['a:Pallets']['a:NetworkPallet'] as $pk => $pv) {
				array_push($line, $pv['a:ConBarCode']);
				array_push($line, $pv['a:PalletSize']);
				array_push($line, $pv['a:PalletType']);
				array_push($line, $pv['a:PltBarcode']);
				
				$pindex++;
			}
			
			for (; $pindex <= 10; $pindex++) {
				array_push($line, "");
			}
			
	        fputcsv($temp_memory, $line, ",");
		}
		
	    /** rewrind the "file" with the csv lines **/
	    fseek($temp_memory, 0);
	    
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="upn.csv";');
	    
	    /** Send file to browser for download */
	    fpassthru($temp_memory);
	    
	} catch (Exception $e) {
	    var_dump($client->__getLastRequestHeaders());
	    var_dump($client->__getLastRequest());
	    var_dump($client->__getLastResponse());
	    var_dump($e);
	}
?>