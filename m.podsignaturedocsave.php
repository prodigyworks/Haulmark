<?php
	require_once("system-db.php");
	require_once('signature-to-image.php');
	require_once("invoiceemail.php");
	require_once("sqlfunctions.php");
	require_once("bookingshared.php");
	
	start_db();
	
	$bookinglegid = $_POST['bookingid'];
	$pallets = $_POST['pallets'];
	$memberid = getLoggedOnMemberID();
	$status = "C";
	$damagedtext = mysql_escape_string($_POST['damagedtext']);
	$damagedimageid = getImageData("damagedimageid");
	
	if ($damagedtext != "") {
		$status = "D";
	}
	
	$sql = "SELECT B.id, B.customerid, B.charge, B.nominalledgercodeid
			FROM {$_SESSION['DB_PREFIX']}bookingleg A
			INNER JOIN {$_SESSION['DB_PREFIX']}booking B
			ON B.id = A.bookingid
			WHERE A.id = $bookinglegid";
	
	$result = mysql_query($sql);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$customerid = $member['customerid'];
			$charge = $member['charge'];
			$bookingid = $member['id'];
			$nominalledgercodeid = $member['nominalledgercodeid'];
		}
		
	} else {
		logError("$sql - " . mysql_error());
	}
	
	$img = null;
	
	if (isset($_POST['output']) && $_POST['output'] != "") {
		$img = sigJsonToImage($_POST['output']);
		
	} else {
		// Create the image
		$img = imagecreatetruecolor(400, 30);
		
		// Create some colors
		$white = imagecolorallocate($img, 255, 255, 255);
		$grey = imagecolorallocate($img, 128, 128, 128);
		$black = imagecolorallocate($img, 0, 0, 0);
		imagefilledrectangle($img, 0, 0, 399, 29, $white);
		
		// The text to draw
		$text = $_POST['name'];
		// Replace path by your own font path
		$font = 'build/journal.ttf';
		
		// Add some shadow to the text
		imagettftext($img, 20, 0, 11, 21, $grey, $font, $text);
		
		// Add the text
		imagettftext($img, 20, 0, 10, 20, $black, $font, $text);
		
		// Using imagepng() results in clearer text compared with imagejpeg()
	}
	
	ob_start();
	imagepng($img);
	$imgstring = ob_get_contents(); 
	ob_end_clean();
	
	$escimgstring = mysql_escape_string($imgstring);
	$id = $_POST['signatureid'];
	
	$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}images 
			(
				mimetype, name, image, createddate
			) 
			VALUES 
			(
				'image/png', 'Signature $id', '$escimgstring', NOW()
			)";
	$result = mysql_query($qry);
	$imageid = mysql_insert_id();
	
	$qry = "UPDATE {$_SESSION['DB_PREFIX']}bookingleg SET 
			signatureid = '$imageid',
			pallets = $pallets,
			status = '$status',
			damagedimageid = $damagedimageid,
			damagedtext = '$damagedtext'
			WHERE id = $bookinglegid ";

	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	/* Check to see if there any more legs 
	   that have not been completed / signed. */
	$incompletecount = 0;
	$sql = "SELECT COUNT(*) AS incompletecount
			FROM {$_SESSION['DB_PREFIX']}bookingleg A
			WHERE A.bookingid = $bookingid
			AND (A.signatureid = 0 OR A.signatureid IS NULL)
			ORDER BY A.id";
	
	$result = mysql_query($sql);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$incompletecount = $member['incompletecount'];
		}
		
	} else {
		logError("$sql - " . mysql_error());
	}
	
	if ($incompletecount == 0) {
		$statusid = 7; /* Complete. */
		
		if ($charge != 0) {
			/* Completed. */
			$sql = "SELECT A.selfbilledinvoices, A.mobileautoinvoice
					FROM {$_SESSION['DB_PREFIX']}customer A 
					WHERE A.id = $customerid";
			$result = mysql_query($sql);
		
			//Check whether the query was successful or not
			if($result) {
				while (($member = mysql_fetch_assoc($result))) {
					$selfbilledinvoices = $member['selfbilledinvoices'];
					$mobileautoinvoice = $member['mobileautoinvoice'];
					
					if ($selfbilledinvoices == "N" && $mobileautoinvoice == "Y") {
						/* Set to invoiced. */
						$statusid = 8;
						$generatedbooking = getSiteConfigData()->bookingprefix . sprintf("%06d", $bookinglegid, 6);
						
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
						$legsummary = mysql_escape_string(getJourneyDescription($bookinglegid));
						
						$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}invoiceitem	
								(
									invoiceid, productid, description, priceeach,
									quantity, linetotal, vat, vatrate, nominalledgercodeid
								)
								VALUES
								(
									$invoiceid, $bookinglegid, '$legsummary', $charge,
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
		
	} else {
		$statusid = 6;
	}
		
	$qry = "UPDATE {$_SESSION['DB_PREFIX']}booking SET 
			statusid = $statusid
			WHERE id = $bookingid ";

	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	mysql_query("COMMIT");
	
	header("location: m.podsignatureconfirm.php");
?>