<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$id = $_POST['id'];
	$qry = null;
	$json = array(); 
	
	$qry = "SELECT A.*, " .
			"DATE_FORMAT(A.requesteddate, '%d/%m/%Y') AS requesteddate, " .
			"DATE_FORMAT(A.startdate, '%d/%m/%Y') AS startdate, " .
			"DATE_FORMAT(A.enddate, '%d/%m/%Y') AS enddate " .
			"FROM {$_SESSION['DB_PREFIX']}holiday A " .
			"WHERE A.id = $id";
	
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$line = array(
					"id" => $member['id'], 
					"memberid" => $member['memberid'], 
					"startdate" => $member['startdate'], 
					"startdate_half" => $member['startdate_half'], 
					"enddate" => $member['enddate'], 
					"enddate_half" => $member['enddate_half'], 
					"requesteddate" => $member['requesteddate'], 
					"acceptedby" => $member['acceptedby'], 
					"accepteddate" => $member['accepteddate'], 
					"rejectedby" => $member['rejectedby'], 
					"rejecteddate" => $member['rejecteddate'], 
					"daystaken" => number_format($member['daystaken'], 1), 
					"reason" => $member['reason']
				);  
			
			array_push($json, $line);
		}
	}
	
	echo json_encode($json); 
?>