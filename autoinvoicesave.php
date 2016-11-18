<?php
	require_once("system-db.php");
	require_once("invoiceemail.php");
	
	start_db();
	
	$yourordernumber = mysql_escape_string($_POST['yourordernumber']);
	$memberid = getLoggedOnMemberID();
	$first = true;
	$total = 0;
	$statusid = 8;
	
	foreach ($_POST['selected'] AS $bookingid) {
		/* Get existing status */
		$sql = "SELECT A.* 
				FROM {$_SESSION['DB_PREFIX']}booking A 
				WHERE A.id = $bookingid";
		$result = mysql_query($sql);
	
		//Check whether the query was successful or not
		if($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$charge = $member['charge'];
				$customerid = $member['customerid'];
				$generatedbooking = getBookingReference($bookingid);
				$legsummary = mysql_escape_string($member['legsummary']);
				$nominalledgercodeid = mysql_escape_string($member['nominalledgercodeid']);
				
				if ($first) {
					$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}invoice
							(
								customerid, revision, orderdate, yourordernumber,
								paid, exported, status, takenbyid, deliverycharge,
								discount, total, downloaded
							)
							VALUES
							(
								$customerid, 1, CURDATE(), '$yourordernumber',
								'N', 'N', 0, 1, 0,
								0, 0, 'N'
							)";
								
					if (! mysql_query($sql)) {
						logError("Error inserting invoice: $sql - " . mysql_error());
					}
					
					$invoiceid = mysql_insert_id();
					$first = false;
				}
				
				$vatrate = getSiteConfigData()->vatrate;
				$vat = $charge * ($vatrate / 100);
				$linetotal = $charge;
				$total += $linetotal;
				
				$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}invoiceitem	
						(
							invoiceid, productid, description, priceeach,
							quantity, linetotal, vat, vatrate, nominalledgercodeid
						)
						VALUES
						(
							$invoiceid, $bookingid, '$legsummary', $charge,
							1, $linetotal, $vat, $vatrate, '$nominalledgercodeid'
						)";
							
				if (! mysql_query($sql)) {
					logError("Error inserting invoice: $sql - " . mysql_error());
				}
				
				$sql = "UPDATE {$_SESSION['DB_PREFIX']}booking SET 
						statusid = $statusid
						WHERE id = $bookingid";
				
				if (! mysql_query($sql)) {
					logError("Error updating booking: $sql - " . mysql_error());
				}
			}
		}
	}

	if (! $first) {
		$sql = "UPDATE {$_SESSION['DB_PREFIX']}invoice SET 
				total = $total
				WHERE id = $invoiceid";
		
		if (! mysql_query($sql)) {
			logError("Error updating booking: $sql - " . mysql_error());
		}
//		
//		try {
//			invoiceEmail($invoiceid);
//			
//		} catch (Exception $e) {
//			/* Ignore */
//		}
	}
	
	mysql_query("COMMIT");
	
	header("location: autoinvoicingconfirm.php?id=$invoiceid");
?>