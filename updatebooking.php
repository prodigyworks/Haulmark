<?php
	require_once("system-db.php");
	require_once("bookingshared.php");
	require_once("invoiceemail.php");
	
	start_db();
	
	$id = $_POST['id'];
	$legs = $_POST['legs'];
	$startdatetime = convertStringToDateTime($_POST['startdatetime'] . " " . $_POST['startdatetime_time']);
	$enddatetime = convertStringToDateTime($_POST['enddatetime'] . " " . $_POST['enddatetime_time']);
	$customerid = $_POST['customerid'];
	$statusid = $_POST['statusid'];
	$memberid = $_POST['memberid'];
	$driverid = $_POST['driverid'];
	$agencydriver = $_POST['agencydriver'];
	$vehicleid = $_POST['vehicleid'];
	$vehicletypeid = $_POST['vehicletypeid'];
	$trailerid = $_POST['trailerid'];
	$drivername = mysql_escape_string($_POST['drivername']);
	$agencyvehicleregistration = mysql_escape_string($_POST['agencyvehicleregistration']);
	$driverphone = mysql_escape_string($_POST['driverphone']);
	$worktypeid = $_POST['worktypeid'];
	$nominalledgercodeid = $_POST['nominalledgercodeid'];
	$loadtypeid = $_POST['loadtypeid'];
	$ordernumber = mysql_escape_string($_POST['ordernumber']);
	$ordernumber2 = mysql_escape_string($_POST['ordernumber2']);
	$miles = $_POST['miles'];
	$duration = $_POST['duration'];
	$pallets = $_POST['pallets'];
	$weight = $_POST['weight'];
	$rate = $_POST['rate'];
	$charge = $_POST['charge'];
	$notes = mysql_escape_string($_POST['notes']);
	$fromplace = $_POST['fromplace'];
	$toplace = $_POST['toplace'];
	$toplace_phone = mysql_escape_string($_POST['toplace_phone']);
	$toplace_ref = mysql_escape_string($_POST['toplace_ref']);
	$fromplace_phone = mysql_escape_string($_POST['fromplace_phone']);
	$fromplace_ref = mysql_escape_string($_POST['fromplace_ref']);
	$currentstatusid = 0;
	
	/* Get existing status */
	$sql = "SELECT statusid, charge 
			FROM {$_SESSION['DB_PREFIX']}booking A 
			WHERE A.id = $id";
	$result = mysql_query($sql);

	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$currentstatusid = $member['statusid'];
			$currentcharge = $member['charge'];
		}
	}
	
	if (($statusid == 7 && $currentstatusid < 7 && $charge != 0) ||
	    ($statusid == 7 && $currentcharge == 0 && $charge != 0)) {
		/* Completed. */
		$sql = "SELECT A.selfbilledinvoices, A.taxcodeid
				FROM {$_SESSION['DB_PREFIX']}customer A 
				WHERE A.id = $customerid";
		$result = mysql_query($sql);
	
		//Check whether the query was successful or not
		if($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$selfbilledinvoices = $member['selfbilledinvoices'];
				
				if ($selfbilledinvoices == "N") {
					/* Set to invoiced. */
					$statusid = 8;
					$generatedbooking = getSiteConfigData()->bookingprefix . sprintf("%06d", $id, 6);
					
					$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}invoice
							(
								customerid, revision, orderdate, yourordernumber,
								paid, exported, status, takenbyid, deliverycharge,
								discount, total, downloaded
							)
							VALUES
							(
								$customerid, 1, CURDATE(), '$generatedbooking',
								'N', 'N', 0, 1, 0,
								0, $charge, 'N'
							)";
								
					if (! mysql_query($sql)) {
						logError("Error inserting invoice: $sql - " . mysql_error());
					}
					
					$invoiceid = mysql_insert_id();
					$vatrate = getSiteConfigData()->vatrate;
					$vat = $charge * ($vatrate / 100);
					$linetotal = $charge;
					
					$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}invoiceitem	
							(
								invoiceid, productid, description, priceeach,
								quantity, linetotal, vat, vatrate, nominalledgercodeid
							)
							VALUES
							(
								$invoiceid, $id, '$legsummary', $charge,
								1, $linetotal, $vat, $vatrate, '$nominalledgercodeid'
							)";
								
					if (! mysql_query($sql)) {
						logError("Error inserting invoice: $sql - " . mysql_error());
					}
					
					try {
						invoiceEmail($invoiceid);
						
					} catch (Exception $e) {
						/* Ignore */
					}
				}
			}
		}
	}
	
	$qry = "UPDATE {$_SESSION['DB_PREFIX']}booking SET 
			startdatetime = '$startdatetime',
			enddatetime = '$enddatetime',
			customerid = '$customerid',
			toplace_phone = '$toplace_phone',
			toplace_ref = '$toplace_ref',
			fromplace_phone = '$fromplace_phone',
			fromplace_ref = '$fromplace_ref',
			statusid = '$statusid',
			memberid = '$memberid',
			driverid = '$driverid',
			vehicleid = '$vehicleid',
			vehicletypeid = '$vehicletypeid',
			trailerid = '$trailerid',
			drivername = '$drivername',
			agencyvehicleregistration = '$agencyvehicleregistration',
			driverphone = '$driverphone',
			worktypeid = '$worktypeid',
			nominalledgercodeid = '$nominalledgercodeid',
			loadtypeid = '$loadtypeid',
			ordernumber = '$ordernumber',
			ordernumber2 = '$ordernumber2',
			miles = '$miles',
			legsummary = '$legsummary',
			duration = '$duration',
			pallets = '$pallets',
			weight = '$weight',
			rate = '$rate',
			charge = '$charge',
			notes = '$notes',
			fromplace = '$fromplace',
			toplace = '$toplace'
			WHERE id = $id ";

	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	$qry = "DELETE FROM {$_SESSION['DB_PREFIX']}bookingleg 
			WHERE bookingid = $id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}

	for ($i = 0; $i < count($_POST['legs']); $i++) {
		$place = mysql_escape_string($_POST['legs'][$i]['place']);
		$time = $_POST['legs'][$i]['time'];
		$departuretime = convertStringToDate($_POST['legs'][$i]['date']) . " $time";
		$reference = mysql_escape_string($_POST['legs'][$i]['reference']);
		$phone = mysql_escape_string($_POST['legs'][$i]['phone']);
		
		$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}bookingleg 
				(
					bookingid, departuretime, place, reference, phone
				)
				VALUES
				(
					$id, '$departuretime', '$place', '$reference', '$phone'
				)";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
	}
	
	$legsummary = mysql_escape_string(getJourneyDescription($id));
	
	$qry = "UPDATE {$_SESSION['DB_PREFIX']}booking SET 
			legsummary = '$legsummary'
			WHERE id = $id ";

	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}	
	
	mysql_query("COMMIT");
?>
