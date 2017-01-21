<?php
	require_once("system-db.php");
	require_once("sqlfunctions.php");
	require_once("businessobjects/BookingClass.php");
	require_once("businessobjects/BookingLegClass.php");
	
	start_db();
	
	$booking = new BookingClass();
	$booking->setMemberid(getLoggedOnMemberID());
	$booking->setCustomerid(getLoggedOnCustomerID());
	$booking->setStatusid(1);
	$booking->setStartdatetime($_POST['startdatetime']);
	$booking->setEnddatetime($_POST['enddatetime']);
	$booking->setVehicleid($_POST['vehicletypeid']);
	$booking->setPallets($_POST['pallets']);
	$booking->setMiles($_POST['miles']);
	$booking->setDuration($_POST['duration']);
	$booking->setNotes(mysql_escape_string($_POST['notes']));
	$booking->setFromplace(getSiteConfigData()->basepostcode);
	$booking->setFromplace_lat($_POST['base_lat']);
	$booking->setFromplace_lng($_POST['base_lng']);
	$booking->setToplace(getSiteConfigData()->basepostcode);
	$booking->setToplace_lat($_POST['base_lat']);
	$booking->setToplace_lng($_POST['base_lng']);
	$booking->setBookingtype("W");
	$booking->setConfirmed("N");
	$booking->setFixedprice("0");
	
	try {
		/* Default costs from base data. */
		$booking->initialiseCostsFromBaseData();
		
		/* Create new booking. */
		$booking->insert();
	
		for ($i = 1; ; $i++) {
			if (isset($_POST['point_' . $i])) {
				$leg = new BookingLegClass();
				$leg->setPlace($_POST['point_' . $i]);
				$leg->setReference($_POST['point_' . $i . "_ref"]);
				$leg->setPhone($_POST['point_' . $i . "_phone"]);
				$leg->setVisittype($_POST['point_' . $i . "_visittype"]);
				$leg->setArrivaltime(convertStringToDate($_POST['pointarrivaldate_' . $i]) . " " . $_POST['pointarrivaltime_' . $i]);
				$leg->setDeparturetime(convertStringToDate($_POST['pointdeparturedate_' . $i]) . " " . $_POST['pointdeparturetime_' . $i]);
				
				$booking->addLeg($leg);
				
			} else {
				break;
			}
		}
	
		/* Leg summary will have changed. */
		$booking->update();
	
	} catch (Exception $e) {
		SQLError($e->getMessage());
	}
	
	if (isset($_FILES['po']) && $_FILES['po']['tmp_name'] != "") {
		$documentid = getFileData("po");
		$memberid = getLoggedOnMemberID();
		
		$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}bookingdocs
				(
					bookingid, documentid, createddate,
					metacreateddate, metamodifieddate,
					metamodifieduserid, metacreateduserid
				)
				VALUES
				(
					{$booking->getId()}, $documentid, NOW(),
					NOW(), NOW(),
					$memberid, $memberid
				)";
					
		if (! mysql_query($sql)) {
			SQLError($sql);
		}
	}
	
	$customername = GetCustomerName($booking->getCustomerid());
	$name = GetUserName();
	$date = date("d/m/Y H:I");
	
	$message = "<h1>Your booking reference is {$booking->getFormattedID()}</h1><br><br>";
	$message .= getSiteConfigData()->webbookingconfirmation;
	
	if (isset($_FILES['po']) && $_FILES['po']['tmp_name'] != "") {
		sendRoleMessage("WEBBOOKING", "Online Booking", $message . "<br><br><h3>Purchase order has been attached</h3>");
		
	} else {
		sendRoleMessage("WEBBOOKING", "Online Booking", $message);
	}
	
	sendCustomerMessage($booking->getCustomerid(), "Online Booking", $message);
	
	mysql_query("COMMIT");
	
	header("location: customerbookingformconfirm.php?id={$booking->getId()}");
?>