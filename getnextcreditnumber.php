<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$json = array();
	
	$qry = "SELECT creditnumber " .
			"FROM {$_SESSION['DB_PREFIX']}quotenumbers";

	$result = mysql_query($qry);
	$found = false;
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$line = array(
					"credit" => $member['creditnumber'] + 1
				);  
			
			array_push($json, $line);
			
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}quotenumbers SET creditnumber = creditnumber + 1, metamodifieddate = NOW(), metamodifieduserid = " . getLoggedOnMemberID();
			$itemresult = mysql_query($sql);
			
			if (! $itemresult) {
				logError($sql . " - " . mysql_error());
			}
			
			$found = true;
		}
		
	} else {
		logError($qry . " - " . mysql_error());
	}
	
	if (! $found) {
		$line = array(
				"credit" => "2000"
			);  
		
		array_push($json, $line);
		
		$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}quotenumbers (invoicenumber, statementnumber, quotenumber, creditnumber, createddate, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid ) VALUES (1999, 1999, 1999, 2000, '" . date("Y-m-d") . "', NOW(), " . getLoggedOnMemberID() . ", NOW(), " .  getLoggedOnMemberID() . ")";
		$itemresult = mysql_query($sql);
		
		if (! $itemresult) {
			logError($sql . " - " . mysql_error());
		}
	}
	
	echo json_encode($json); 
?>