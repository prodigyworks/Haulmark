<?php
	require_once("system-db.php");
	
	function createNewBookingFromExisting($id, $bookingdate, $vehicleid, $ordernumber) {
		start_db();
		
		$memberid = getLoggedOnMemberID();
		
		$sql = "SELECT startdatetime 
				FROM {$_SESSION['DB_PREFIX']}booking
			    WHERE id = $id";
		$result = mysql_query($sql);
		
		if (! $result) {
			logError($sql . " - " . mysql_error());
		}
		
		$offset = 0;
		
		while (($member = mysql_fetch_assoc($result))) {
			$offset = $member['startdatetime'];
		}
		
		$diff = (strtotime($bookingdate) - strtotime($offset));
		$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}booking
				(
					vehicleid, driverid, trailerid, customerid, loadtypeid, vehicletypeid, 
					depotid, worktypeid, pallets, items, memberid, startdatetime, enddatetime, 
					dsdatetime, rate, charge, weight, miles, vehiclecostoverhead, allegrodayrate, 
					agencydayrate, wages, fuelcostoverhead, maintenanceoverhead, profitmargin, 
					customercostpermile, bookingtype, postedtosage, statusid, legsummary, duration, 
					ordernumber, ordernumber2, drivername, driverphone, fromplace, toplace, 
					fromplace_lat, fromplace_lng, fromplace_phone, fromplace_ref, toplace_lat, 
					toplace_lng, toplace_phone, toplace_ref, totalmiles, totaltimehrs, notes,
					fixedprice, nominalledgercodeid,
					metacreateddate, metamodifieddate, metamodifieduserid, metacreateduserid
				)
				SELECT
					$vehicleid, 0, 0, customerid, 0, 0, 
					depotid, 0, 0, 0, $memberid, DATE_ADD(startdatetime, INTERVAL $diff SECOND), 
					DATE_ADD(enddatetime, INTERVAL $diff SECOND), DATE_ADD(dsdatetime, INTERVAL $diff SECOND), 
					rate, charge, 0, miles, vehiclecostoverhead, allegrodayrate, 
					agencydayrate, wages, fuelcostoverhead, maintenanceoverhead, profitmargin, 
					customercostpermile, 0, postedtosage, 1, legsummary, duration, 
					'$ordernumber', '', '', '', fromplace, toplace, 
					fromplace_lat, fromplace_lng, fromplace_phone, fromplace_ref, toplace_lat, 
					toplace_lng, toplace_phone, toplace_ref, totalmiles, totaltimehrs, notes, 
					fixedprice, nominalledgercodeid,
					NOW(), NOW(), $memberid, $memberid
				FROM {$_SESSION['DB_PREFIX']}booking
				WHERE id = $id";
		$result = mysql_query($sql);
		
		if (! $result) {
			logError($sql . " - " . mysql_error());
		}
		
		$newid = mysql_insert_id();		
		
		$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}bookingleg
				(
					bookingid, arrivaltime, 
					departuretime, 
					arrivaltime, 
					place, reference,
					phone, place_lng, place_lat, timetaken, miles
				)
				SELECT
					$newid, '$bookingdate', 
					DATE_ADD(departuretime, INTERVAL $diff SECOND), 
					DATE_ADD(arrivaltime, INTERVAL $diff SECOND), 
					place, reference,
					phone, place_lng, place_lat, timetaken, miles
				FROM {$_SESSION['DB_PREFIX']}bookingleg
				WHERE bookingid = $id
				ORDER BY id";
		$legresult = mysql_query($sql);
		
		if (! $legresult) {
			logError($sql . " - " . mysql_error());
		}
	}

?>