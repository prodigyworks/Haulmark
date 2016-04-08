<?php 
	include "system-db.php";
	
	start_db();
	
	$startdate = ($_GET['from']);
	$enddate = ($_GET['to']);
	
	$sql ="SELECT A.id, A.startdatetime, A.enddatetime, A.trailerid, A.driverid, 
		   A.vehicleid, A.ordernumber, A.bookingtype, A.fromplace, A.toplace,
		   B.name AS drivername, 
		   C.registration AS vehiclename, C.registration, 
		   D.registration AS trailername,
		   E.fgcolour, E.bgcolour
		   FROM {$_SESSION['DB_PREFIX']}booking A 
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}driver B 
		   ON B.id = A.driverid 
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}vehicle C 
		   ON C.id = A.vehicleid 
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}trailer D 
		   ON D.id = A.trailerid
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}bookingstatus E 
		   ON E.id = A.statusid
		   WHERE ((A.startdatetime >= '$startdate' AND A.startdatetime <= '$enddate') 
		   OR (A.enddatetime >= '$startdate' AND A.enddatetime <= '$enddate'))
		   AND A.statusid IN (3, 4, 5, 6, 7, 8, 9)";
	$result = mysql_query($sql);
	$first = true;
	$json = array();
	
	if ($_GET['mode'] == "V") {
		$sectionid = "vehicleid";

	} else if ($_GET['mode'] == "D") {
		$sectionid = "driverid";

	} else if ($_GET['mode'] == "T") {
		$sectionid = "trailerid";
	}
	
	
	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
				
			$sql ="SELECT A.place, A.departuretime, A.id
				   FROM {$_SESSION['DB_PREFIX']}bookingleg A
				   WHERE A.bookingid = " . $member['id'];
			$itemresult = mysql_query($sql);
			
			$previousdate = $member['startdatetime'];
			$previousplace = str_replace(", United Kingdom", "", $member['fromplace']);
			
			//Check whether the query was successful or not
			if($itemresult) {
				while (($itemmember = mysql_fetch_assoc($itemresult))) {
					$place = str_replace(", United Kingdom", "", $itemmember['place']);
					
					
					array_push(
							$json, 
							array(
									"id" => "Leg:" . $itemmember['id'],
									"color" => $member['bgcolour'],
									"textColor" => $member['fgcolour'],
									"start_date" => $previousdate,
									"end_date" => $itemmember['departuretime'],
									"text" => $previousplace . " -> " . $place,
									"section_id" => $member[$sectionid]
								)
						);
						
					$previousdate = $itemmember['departuretime'];
					$previousplace = $place;
				}
				
			} else {
				logError($sql . " - " . mysql_error());
			}
		
			array_push(
				$json, 
				array(
						"id" => $member['id'],
						"color" => $member['bgcolour'],
						"textColor" => $member['fgcolour'],
						"start_date" => $previousdate,
						"end_date" => $member['enddatetime'],
						"text" => $previousplace . " -> " . $member['toplace'],
						"section_id" => $member['vehicleid']
					)
			);
			
		}

	} else {
		logError($sql . " - " . mysql_error());
	}
	
	if ($_GET['mode'] == "D") {
		$sql ="SELECT A.* FROM {$_SESSION['DB_PREFIX']}holiday A 
			   WHERE A.accepteddate IS NOT NULL
			   AND 
			   (
			   	('$startdate' BETWEEN A.startdate AND A.enddate) 
			   	OR
   			   	('$enddate' BETWEEN A.startdate AND A.enddate) 
   			   )";
		$result = mysql_query($sql);	
		
		if (! $result) {
			logError($sql . " - " . mysql_error());
		}
		
		while (($member = mysql_fetch_assoc($result))) {
			$nstartdate = $member['startdate'];
			$nenddate = $member['enddate'];
			
			if ($member['startdate_half'] == 0) {
				$nstartdate .= " 12:00:00";
				
			} else {
				$nstartdate .= " 00:00:00";
			}
			
			if ($member['enddate_half'] == 0) {
				$nenddate .= " 12:00:00";
				
			} else {
				$nenddate .= " 23:59:00";
			}
			
			array_push(
					$json, 
					array(
							"id" => "HOL" . $member['id'],
							"color" => "#00FFFF",
							"textColor" => "#000000",
							"start_date" => $nstartdate,
							"end_date" => $nenddate,
							"text" => "Holiday ",
							"section_id" => $member['memberid']
						)
				);
		}
		
		$sql ="SELECT A.* FROM {$_SESSION['DB_PREFIX']}absence A 
			   WHERE A.accepteddate IS NOT NULL
			   AND 
			   (
			   	('$startdate' BETWEEN A.startdate AND A.enddate) 
			   	OR
   			   	('$enddate' BETWEEN A.startdate AND A.enddate) 
   			   )";
		$result = mysql_query($sql);	
		
		if (! $result) {
			logError($sql . " - " . mysql_error());
		}
		
		while (($member = mysql_fetch_assoc($result))) {
			$nstartdate = $member['startdate'];
			$nenddate = $member['enddate'];
			
			if ($member['startdate_half'] == 0) {
				$nstartdate .= " 12:00:00";
				
			} else {
				$nstartdate .= " 00:00:00";
			}
			
			if ($member['enddate_half'] == 0) {
				$nenddate .= " 12:00:00";
				
			} else {
				$nenddate .= " 23:59:00";
			}
			
			array_push(
					$json, 
					array(
							"id" => "ABS" . $member['id'],
							"color" => "#FF00FF",
							"textColor" => "#000000",
							"start_date" => $nstartdate,
							"end_date" => $nenddate,
							"text" => "Absence (" . $member['absencetype'] . ") ",
							"section_id" => $member['memberid']
						)
				);
		}
	}
		
	if ($_GET['mode'] == "T") {
		$sql ="SELECT A.*, B.name
			   FROM {$_SESSION['DB_PREFIX']}trailerunavailability A 
			   INNER JOIN {$_SESSION['DB_PREFIX']}trailerunavailabilityreasons B
			   ON B.id = A.reasonid
			   WHERE  
			   (
			   	('$startdate' BETWEEN DATE(A.startdate) AND DATE(A.enddate)) 
			   	OR
   			   	('$enddate' BETWEEN DATE(A.startdate) AND DATE(A.enddate)) 
      		   )";
		$result = mysql_query($sql);	
		
		if (! $result) {
			logError($sql . " - " . mysql_error());
		}
		
		while (($member = mysql_fetch_assoc($result))) {
			$nstartdate = $member['startdate'];
			$nenddate = $member['enddate'];
			
			array_push(
					$json, 
					array(
							"id" => "TR" . $member['id'],
							"color" => "#CCFFCC",
							"textColor" => "#000000",
							"start_date" => $nstartdate,
							"end_date" => $nenddate,
							"text" => "Unavailable (" . $member['name'] . ")",
							"section_id" => $member['trailerid']
						)
				);
		}
	}	
	
	if ($_GET['mode'] == "V") {
		$sql ="SELECT A.*, B.name
			   FROM {$_SESSION['DB_PREFIX']}vehicleunavailability A 
			   INNER JOIN {$_SESSION['DB_PREFIX']}vehicleunavailabilityreasons B
			   ON B.id = A.reasonid
			   WHERE  
			   (
			   	('$startdate' BETWEEN DATE(A.startdate) AND DATE(A.enddate)) 
			   	OR
   			   	('$enddate' BETWEEN DATE(A.startdate) AND DATE(A.enddate)) 
   			   )";
		$result = mysql_query($sql);	
			
		if (! $result) {
			logError($sql . " - " . mysql_error());
		}
		
		while (($member = mysql_fetch_assoc($result))) {
			$nstartdate = $member['startdate'];
			$nenddate = $member['enddate'];
			
			array_push(
					$json, 
					array(
							"id" => "VE" . $member['id'],
							"color" => "#00FFCC",
							"textColor" => "#000000",
							"start_date" => $nstartdate,
							"end_date" => $nenddate,
							"text" => "Unavailable (" . $member['name'] . ")",
							"section_id" => $member['vehicleid']
						)
				);
		}
	}
	
	echo json_encode($json);
?>