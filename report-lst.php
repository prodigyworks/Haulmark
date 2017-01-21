<?php
	require_once("system-db.php");
	
	start_db();
	
	$filename = "lst-report-" . date("Y-m-d");
	
	header("Content type: text/csv;");
    header("Content-Disposition: attachment; filename=\"$filename.csv\";" );
	$list = array ();
	$titles = array(
			"Job Code",
			"Client",
			"Date",
			"Day of the week",
			"Company Trailer ID",
			"Origin Location",
			"Depart",
			"Arrive",
			"Destination Location",
			"Leg Type",
			"Dist RAW",
			"Dist km",
			"Incidents record detail in incident log",
			"Type of Goods",
			"MOA",
			"Quantity",
			"Goods Wt",
			"Est Volume Utilised",
			"Est Deck Utilised",
			"Wt Limited",
			"Multi-Drop",
			"Blank=not multi-drop"
		);	
	
	$fp = fopen('php://output', 'w');
	fputcsv($fp, $titles);
		
	$sql = "SELECT 
			A.*, 
			DATE_FORMAT(A.startdatetime, '%d/%m/%Y') AS startdate,
			DATE_FORMAT(A.startdatetime, '%d/%m/%Y %H:%I') AS startdatetime,
			DAYOFWEEK(A.startdatetime) AS sddayofweek,
			B.name AS customername,
			D.registration AS trailername
			FROM {$_SESSION['DB_PREFIX']}booking A
			INNER JOIN {$_SESSION['DB_PREFIX']}customer B
			ON B.id = A.customerid 
			INNER JOIN {$_SESSION['DB_PREFIX']}trailer D
			ON D.id = A.trailerid 
			ORDER BY A.id";
	
	$result = mysql_query($sql);
	
	while (($resultset = mysql_fetch_assoc($result))) {
		$row = array(
				getBookingReference($resultset['id']),
				$resultset['customername'],
				$resultset['startdate'],
				$resultset['sddayofweek'],
				$resultset['trailername'],
				$resultset['fromplace'],
				$resultset['startdatetime'],
				"Arrive",
				"Destination Location",
				"Leg Type",
				"Dist RAW",
				"Dist km",
				"Incidents record detail in incident log",
				"Type of Goods",
				"MOA",
				"Quantity",
				"Goods Wt",
				"Est Volume Utilised",
				"Est Deck Utilised",
				"Wt Limited",
				"Multi-Drop",
				"Blank=not multi-drop"
			);	
			
		array_push($list, $row);
	}
	
	foreach ($list as $fields) {
	    fputcsv($fp, $fields);
	}
	
	fclose($fp);
?>