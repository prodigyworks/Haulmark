<?php
	require_once("system-db.php");
	require_once("bookingshared.php");
	require_once("sqlfunctions.php");
	
	start_db();
	
	$memberid = getLoggedOnMemberID();
	$customerid = getLoggedOnCustomerID();
	$statusid = 1;
	$startdatetime = $_POST['startdatetime'];
	$enddatetime = $_POST['enddatetime'];
	$vehicletypeid = $_POST['vehicletypeid'];
	$pallets = $_POST['pallets'];
	$baselng = $_POST['base_lng'];
	$baselat = $_POST['base_lat'];
	$base = getSiteConfigData()->basepostcode;
	$notes = mysql_escape_string($_POST['notes']);
	
	$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}booking
			(
				customerid, vehicletypeid, pallets,
				startdatetime, enddatetime, statusid,
				fromplace, fromplace_lat, fromplace_lng,
				fromplace_phone, fromplace_ref,
				toplace, toplace_lat, toplace_lng,
				toplace_phone, toplace_ref,
				memberid, notes,
				metacreateddate, metamodifieddate,
				metamodifieduserid, metacreateduserid
			)
			VALUES
			(
				$customerid, $vehicletypeid, $pallets,
				'$startdatetime', '$enddatetime', $statusid,
				'$base', '$base_lat', '$base_lng',
				'', '',
				'$base', '$base_lat', '$base_lng',
				'', '',
				$memberid, '$notes',
				NOW(), NOW(),
				$memberid, $memberid
			)";
				
	if (! mysql_query($sql)) {
		logError("$sql - " . mysql_error());
	}
	
	$bookingid = mysql_insert_id();

	for ($i = 1; ; $i++) {
		if (isset($_POST['point_' . $i])) {
			$point = $_POST['point_' . $i];
			$pointlat = $_POST['point_' . $i . "_lat"];
			$pointlng = $_POST['point_' . $i . "_lng"];
			$pointdeparturedate = convertStringToDate($_POST['pointdeparturedate_' . $i]);
			$pointdeparturetime = $_POST['pointdeparturetime_' . $i];
			$pointdeparturedate = $pointdeparturedate . " " . $pointdeparturetime;
			$pointarrivaldate = convertStringToDate($_POST['pointarrivaldate_' . $i]);
			$pointarrivaltime = $_POST['pointarrivaltime_' . $i];
			$pointarrivaldate = $pointarrivaldate . " " . $pointarrivaltime;
			$phone = $_POST['point_' . $i . "_phone"];
			$reference = $_POST['point_' . $i . "_ref"];
			
			addLeg($bookingid, $point, $pointlng, $pointlat, $pointarrivaldate, $pointdeparturedate, $phone, $reference);
			
		} else {
			break;
		}
	}
	
	$legsummary = getJourneyDescription($bookingid);
	
	$sql = "UPDATE {$_SESSION['DB_PREFIX']}booking SET 
			legsummary = '$legsummary' 
			WHERE id = $bookingid";
	
	if (! mysql_query($sql)) {
		logError($sql . " - " . mysql_error());
	}
	
	if (isset($_FILES['po']) && $_FILES['po']['tmp_name'] != "") {
		$documentid = getFileData("po");
		
		$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}bookingdocs
				(
					bookingid, documentid, createddate,
					metacreateddate, metamodifieddate,
					metamodifieduserid, metacreateduserid
				)
				VALUES
				(
					$bookingid, $documentid, NOW(),
					NOW(), NOW(),
					$memberid, $memberid
				)";
					
		if (! mysql_query($sql)) {
			logError("$sql - " . mysql_error());
		}
	}
	
	$customername = GetCustomerName($customerid);
	$name = GetUserName();
	$date = date("d/m/Y H:I");
	
	$message = "<h1>Your booking reference is " . getBookingReference($bookingid) . "</h1><br><br>";
	$message .= getSiteConfigData()->webbookingconfirmation;
	
	if (isset($_FILES['po']) && $_FILES['po']['tmp_name'] != "") {
		sendRoleMessage("WEBBOOKING", "Online Booking", $message . "<br><br><h3>Purchase order has been attached</h3>");
		
	} else {
		sendRoleMessage("WEBBOOKING", "Online Booking", $message);
	}
	
	sendCustomerMessage($customerid, "Online Booking", $message);
	
	mysql_query("COMMIT");
	
	header("location: customerbookingformconfirm.php?id=$bookingid");
	
	function addLeg($id, $point, $pointlng, $pointlat, $pointarrivaldate, $pointdeparturedate, $phone, $reference) {
		$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}bookingleg
				(
					bookingid, place, place_lng, place_lat, 
					arrivaltime, departuretime, 
					phone, reference
				)
				VALUES
				(
					$id, '$point', $pointlng, $pointlat, 
					'$pointarrivaldate', '$pointdeparturedate', 
					'$phone', '$reference'
				)";
		$result = mysql_query($sql);

		if (! $result) {
			logError($sql . " - " . mysql_error());
		}
	}
?>