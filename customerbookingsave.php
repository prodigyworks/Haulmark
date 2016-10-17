<?php
	require("system-db.php");
	require("bookingshared.php");
	
	start_db();
	
	$memberid = getLoggedOnMemberID();
	$customerid = getLoggedOnCustomerID();
	$statusid = 1;
	$vehicletypeid = $_POST['vehicletypeid'];
	$pallets = $_POST['pallets'];
	$startdatetime = convertStringToDate($_POST['startdatetime']) . " " . $_POST['startdatetime_time'];
	$enddatetime = convertStringToDate($_POST['enddatetime']) . " " . $_POST['enddatetime_time'];
	$fromplace = mysql_escape_string($_POST['fromplace']);
	$fromplace_lat = mysql_escape_string($_POST['fromplace_lat']);
	$fromplace_lng = mysql_escape_string($_POST['fromplace_lng']);
	$fromplace_phone = mysql_escape_string($_POST['fromplace_phone']);
	$fromplace_ref = mysql_escape_string($_POST['fromplace_ref']);
	$toplace = mysql_escape_string($_POST['toplace']);
	$toplace_lat = mysql_escape_string($_POST['toplace_lat']);
	$toplace_lng = mysql_escape_string($_POST['toplace_lng']);
	$toplace_phone = mysql_escape_string($_POST['toplace_phone']);
	$toplace_ref = mysql_escape_string($_POST['toplace_ref']);
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

	addLeg(
			$bookingid, 
			$fromplace, 
			$fromplace_lng, 
			$fromplace_lat,  
			$startdatetime, 
			$fromplace_phone, 
			$fromplace_ref
		);
	
	for ($i = 1; ; $i++) {
		if (isset($_POST['point_' . $i])) {
			$point = $_POST['point_' . $i];
			$pointlat = $_POST['point_' . $i . "_lat"];
			$pointlng = $_POST['point_' . $i . "_lng"];
			$pointdate = convertStringToDate($_POST['pointdate_' . $i]);
			$pointtime = $_POST['pointtime_' . $i];
			$pointdate = $pointdate . " " . $pointtime;
			$phone = $_POST['point_' . $i . "_phone"];
			$reference = $_POST['point_' . $i . "_ref"];
			
			addLeg($bookingid, $point, $pointlng, $pointlat, $pointdate, $phone, $reference);
			
		} else {
			break;
		}
	}
	
	addLeg(
			$bookingid, 
			$toplace, 
			$toplace_lng, 
			$toplace_lat,  
			$enddatetime, 
			$toplace_phone, 
			$toplace_ref
		);
	
	$legsummary = getJourneyDescription($bookingid);
	
	$sql = "UPDATE {$_SESSION['DB_PREFIX']}booking SET 
			legsummary = '$legsummary' 
			WHERE id = $bookingid";
	
	if (! mysql_query($sql)) {
		logError($sql . " - " . mysql_error());
	}
	
	$customername = GetCustomerName($customerid);
	$name = GetUserName();
	$date = date("d/m/Y H:I");
	
	$message = "A online booking had been made by $customername ($name) on $date";
	sendRoleMessage("WEBBOOKING", "Online Booking", $message);
	
	mysql_query("COMMIT");
	
	header("location: customerbookingformconfirm.php");
	
	function addLeg($id, $point, $pointlng, $pointlat, $pointdate, $phone, $reference) {
		$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}bookingleg
				(
					bookingid, place, place_lng, place_lat, 
					departuretime, phone, reference
				)
				VALUES
				(
					$id, '$point', $pointlng, $pointlat, 
					'$pointdate', '$phone', '$reference'
				)";
		$result = mysql_query($sql);

		if (! $result) {
			logError($sql . " - " . mysql_error());
		}
	}
?>