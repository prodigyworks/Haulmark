<?php
	require_once("system-db.php");
	require_once("sqlfunctions.php");
	
	start_db();
	
	$bookingid = $_POST['bookingid'];
	$documentid = getFileData("imagefile");
	$memberid = getLoggedOnMemberID();
	$customerid = 0;

	if ($_POST['reference'] == "") {
		$reference = getSiteConfigData()->bookingprefix . sprintf("%06d", $bookingid);
		
	} else {
		$reference = $_POST['reference'];
	}
	
	$sql = "SELECT customerid
			FROM {$_SESSION['DB_PREFIX']}booking
			WHERE id = $bookingid";
	
	$result = mysql_query($sql);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$customerid = $member['customerid'];
		}
		
	} else {
		logError("$sql - " . mysql_error());
	}
	
	$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}bookingpod
			(
				bookingid, documentid, poddate, reference, 
				metacreateddate, metamodifieddate,
				metacreateduserid, metamodifieduserid
			)
			VALUES
			(
				$bookingid, $documentid, CURDATE(), '$reference', 
				NOW(), NOW(),
				$memberid, $memberid
			)";
				
	if (! mysql_query($sql)) {
		logError("$sql - " . mysql_error());
	}
		
	$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}customerpod
			(
				customerid, documentid, poddate, reference, 
				metacreateddate, metamodifieddate,
				metacreateduserid, metamodifieduserid
			)
			VALUES
			(
				$customerid, $documentid, CURDATE(), '$reference', 
				NOW(), NOW(),
				$memberid, $memberid
			)";
				
	if (! mysql_query($sql)) {
		logError("$sql - " . mysql_error());
	}
	
	mysql_query("COMMIT");
	
	header("location: m.uploadpodconfirm.php");
?>