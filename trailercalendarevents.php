<?php
	require_once("system-db.php");
	
	start_db();
	
	$json = array();
	$supplierid = getLoggedOnSupplierID();
	$and = "";
	
	if ($supplierid != 0) {
		$and = "AND A.supplierid = $supplierid";
	}
	
	$end = gmdate("Y-m-d\TH:i:s\Z", $_POST['end']);
	$start = gmdate("Y-m-d\TH:i:s\Z", $_POST['start']);
	$sql = "SELECT A.*, B.name, C.registration
			FROM {$_SESSION['DB_PREFIX']}trailerunavailability A
			INNER JOIN {$_SESSION['DB_PREFIX']}trailerunavailabilityreasons B
			ON B.id = A.reasonid
			INNER JOIN {$_SESSION['DB_PREFIX']}trailer C
			ON C.id = A.trailerid
			WHERE DATE(A.startdate) < '$end'
			AND DATE(A.enddate) > '$start'
			$and
			ORDER BY A.id";
	
	$result = mysql_query($sql);
	
	if (! $result) {
		logError("$sql - " . mysql_error());
	}
	
	while (($member = mysql_fetch_assoc($result))) {
		if ($member['status'] == "C") {
			$status = "Complete";
			
		} else if ($member['status'] == "I") {
			$status = "In Progress";
			
		} else if ($member['status'] == "A") {
			$status = "Awaiting Order Number";
					
		} else {
			$status = "Scheduled";
		}
		
		array_push(
				$json, 
				array(
						"id" 				=> $member['id'],
						"title" 			=> $member['registration'] . " - " . $member['name'] . " (" . $status . ")",
						"start" 			=> $member['startdate'],
						"end" 				=> $member['enddate'],
						"className"			=> "eventcolor_" . $member['status'],
						"allDay" 			=> false
				)
			);
	}
	
	// sending the encoded result to success page
	echo json_encode($json);
?>