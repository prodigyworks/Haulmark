<?php 
	include "system-db.php";
	include "bookingshared.php";
	
	start_db();
	
	$startdate = ($_GET['from']);
	$enddate = ($_GET['to']);
	
	$_SESSION['BOOKING_GANTT'] = $startdate;
	
	if ($_GET['mode'] == "V") {
		$sectionid = "vehicleid";

	} else if ($_GET['mode'] == "D") {
		$sectionid = "driverid";

	} else if ($_GET['mode'] == "T") {
		$sectionid = "trailerid";
	}
	$json = array();
	
	$sql ="SELECT A.id, A.startdatetime, A.enddatetime, A.trailerid, A.driverid, 
		   A.vehicleid, A.ordernumber, A.fromplace, A.toplace, A.legsummary,
		   B.name AS drivername, 
		   C.registration AS vehiclename, C.registration, 
		   D.registration AS trailername,
		   E.fgcolour, E.bgcolour,
		   F.accountcode AS customername,
		   F.name AS accountname,
		   (TIMEDIFF(enddatetime, startdatetime) / 10000) AS totalhours
		   FROM {$_SESSION['DB_PREFIX']}booking A 
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}driver B 
		   ON B.id = A.driverid 
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}vehicle C 
		   ON C.id = A.vehicleid 
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}trailer D 
		   ON D.id = A.trailerid
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}bookingstatus E 
		   ON E.id = A.statusid
		   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}customer F 
		   ON F.id = A.customerid 
		   WHERE (A.startdatetime < '$enddate' AND A.enddatetime > '$startdate')
		   AND A.statusid > 1";
	$result = mysql_query($sql);
	
	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$id = $member['id'];
			$totalhours = $member['totalhours'];
			$utilisationhours = 0;
			
			$sql ="SELECT (TIMEDIFF(B.enddatetime, A.departuretime) / 10000) AS utilisationhours
				   FROM {$_SESSION['DB_PREFIX']}bookingleg A 
				   LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}booking B 
				   ON B.id = A.bookingid
				   WHERE A.bookingid = $id
				   ORDER BY A.id DESC
				   LIMIT 1";
			$itemresult = mysql_query($sql);
			
			//Check whether the query was successful or not
			if($itemresult) {
				while (($itemmember = mysql_fetch_assoc($itemresult))) {
					$utilisationhours = $itemmember['utilisationhours'];
				}
			}
			
			$hours = number_format(($utilisationhours * (100 / $totalhours)), 0);
			$lefthours = 100 - $hours;
			
			$tooltip = "Booking: " . getBookingReference($member['id']) . "\n" . 
					   "Customer: " . $member['accountname'] . "\n" . 
					   "Vehicle: " . $member['vehiclename'] . "\n" . 
					   "Trailer: " . $member['trailername'] . "\n" .
					   "Driver: " . $member['drivername'] . "\n" .
					   "Utilisation: $lefthours %";
			
			array_push(
				$json, 
				array(
						"id" => $id,
						"type" => "B",
						"bookingid" => $id,
						"color" => $member['bgcolour'],
						"textColor" => $member['fgcolour'],
						"start_date" => $member['startdatetime'],
						"end_date" => $member['enddatetime'],
						"text" => "<div bookingid='$id' class='bookingcell2' title='$tooltip'>" . $member['customername'] . ": " . $member['legsummary'] . "</div><div class='bookingcell4' bookingid='$id' title='$tooltip'><div bookingid='$id' utilisation='$hours' style='width:$hours%' class='bookingcell3'>&nbsp;</div></div>",
						"section_id" => $member[$sectionid]
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
							"type" => "H",
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
							"type" => "T",
							"color" => "#FF0000",
							"textColor" => "white",
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
							"type" => "V",
							"color" => "#FF0000",
							"textColor" => "white",
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