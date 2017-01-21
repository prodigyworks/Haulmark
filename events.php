<?php 
	require_once("system-db.php");
	require_once("businessobjects/BookingCollectionClass.php");
	require_once("businessobjects/BookingClass.php");
	require_once("businessobjects/BookingLegClass.php");
	
	start_db();
	
	$startdate = ($_GET['from']);
	$enddate = ($_GET['to']);
	
	$_SESSION['BOOKING_GANTT'] = $startdate;
	
	$json = array();
	
	$bookings = new BookingCollectionClass();
	$bookings->loadPlanningPage($startdate, $enddate);
	
	foreach ($bookings->getBookings() AS $booking) {
		/* Calculate hours. */
		$hours = $booking->getUtilisationPercentage();
		$lefthours = 100 - $hours;
			
		if ($_GET['mode'] == "V") {
			$sectionid = $booking->getVehicleid();
		
		} else if ($_GET['mode'] == "D") {
			$sectionid = $booking->getDriverid();
		
		} else if ($_GET['mode'] == "T") {
			$sectionid = $booking->getTrailerid();
		}
			
		$tooltip = "Booking: {$booking->getFormattedID()}\n" . 
				   "Customer: {$booking->getCustomer()->getName()}\n" . 
				   "Vehicle: {$booking->getVehicle()->getRegistration()}\n" . 
				   "Trailer: {$booking->getTrailer()->getRegistration()}\n" .
				   "Driver: {$booking->getDriver()->getName()}\n" .
				   "Journey: {$booking->getLegsummary()}\n" .
				   "Charge: &pound;{$booking->getCharge()}\n" .
				   "Utilisation: $hours %";
		
		array_push(
				$json, 
				array(
						"id" => $booking->getId(),
						"type" => "B",
						"bookingid" => $booking->getId(),
						"color" => $booking->getStatus()->getBgcolour(),
						"textColor" => $booking->getStatus()->getFgcolour(),
						"start_date" => $booking->getStartdatetime(),
						"end_date" => $booking->getEnddatetime(),
						"text" => "<div bookingid='{$booking->getId()}' class='bookingcell2' title='$tooltip'>{$booking->getCustomer()->getName()}: {$booking->getLegsummary()}</div><div class='bookingcell4' bookingid='{$booking->getId()}' title='$tooltip'><div bookingid='{$booking->getId()}' utilisation='$lefthours' style='width:$lefthours%' class='bookingcell3'>&nbsp;</div></div>",
						"section_id" => $sectionid
					)
			);
		
	}
	
	if ($_GET['mode'] == "D") {
		$sql ="SELECT A.*, B.driverid FROM {$_SESSION['DB_PREFIX']}holiday A  
			   INNER JOIN {$_SESSION['DB_PREFIX']}members B
			   ON B.member_id = A.memberid
			   INNER JOIN {$_SESSION['DB_PREFIX']}driver C
			   ON C.id = B.driverid
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
							"section_id" => $member['driverid']
						)
				);
		}
		
		$sql ="SELECT A.*, B.driverid FROM {$_SESSION['DB_PREFIX']}absence A 
			   INNER JOIN {$_SESSION['DB_PREFIX']}members B
			   ON B.member_id = A.memberid
			   INNER JOIN {$_SESSION['DB_PREFIX']}driver C
			   ON C.id = B.driverid
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
							"type" => "A",
							"color" => "#FF00FF",
							"textColor" => "#000000",
							"start_date" => $nstartdate,
							"end_date" => $nenddate,
							"text" => "Absence (" . $member['absencetype'] . ") ",
							"section_id" => $member['driverid']
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
