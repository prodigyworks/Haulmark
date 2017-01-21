<?php
	require_once("system-db.php");
	require_once('signature-to-image.php');
	require_once("invoiceemail.php");
	require_once("sqlfunctions.php");
	require_once("businessobjects/BookingClass.php");
	require_once("businessobjects/BookingLegClass.php");
	
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
	
	$booking = new BookingClass();
	$booking->load($bookingid);
	
	if ($incompletecount == 0) {
		$booking->setStatusid(7); /* Complete. */
		
	} else {
		$statusid = 6;
		$booking->setStatusid(6);
	}
	
	$booking->update();
		
	mysql_query("COMMIT");
	
	header("location: m.podsignatureconfirm.php");
?>