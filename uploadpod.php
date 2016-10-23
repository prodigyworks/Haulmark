<?php
	include("system-db.php");
	
	start_db();
	
	$file = $_POST['file'];
	$folder = $_POST['folder'];
	
	if ($file == "Thumbs.db") {
		exit();
	}
	
	$customerid = 0;
	$customername;
	$bookinglink = false;
	$reference = substr($file, 0, lastIndexOf($file, "."));
	$bookingid = substr($file, 0, strpos($file, "-"));
	
	if (is_numeric ($bookingid)) {
		$reference = substr($file, strpos($file, "-") + 1, lastIndexOf($file, "."));
		$bookinglink = true;
		
	} else {
		$bookingid = 0;
	}
	
    $tmpName  = $_FILES['content']['tmp_name'];  
    $content = "";
       
    // Read the file 
    $fp = fopen($tmpName, 'rb');
      
	while (!feof($fp)) {
		$content .= fread($fp, 192);
	}
      
    fclose($fp);
	
	$sql = "SELECT id, name
			FROM  {$_SESSION['DB_PREFIX']}customer
			WHERE podfolder = '$folder'";
				 
	$result = mysql_query($sql);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$customerid = $member['id'];
			$customername = $member['name'];
		} 
	} else {
		logError(mysql_error() . " - $sql");
	}
	
	if ($customerid == 0) {
		exit(0);
	}
	
	if ($bookinglink) {
		$sql = "SELECT id
				FROM  {$_SESSION['DB_PREFIX']}customerpod
				WHERE customerid = $customerid
				AND reference = '$reference'
				AND bookingid = $bookingid";
		
	} else {
		$sql = "SELECT id
				FROM  {$_SESSION['DB_PREFIX']}customerpod
				WHERE customerid = $customerid
				AND reference = '$reference'";
	}
				 
	$result = mysql_query($sql);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
//			if ($customerid == 444) {
//				logError("POD NOT inserted for $customername, Ref : $reference - $sql", false);
//				mysql_query("COMMIT");
//			}
			exit(0);
		} 
		
	} else {
		logError(mysql_error() . " - $sql");
	}
    
	
	$length = strlen($content);
	$content = gzcompress($content, 9);
    $content = mysql_escape_string($content);
    	
	$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}documents
			(
				name, filename, mimetype, size, image, compressed
			)
			VALUES
			(
				'$file', '$file', 'application/pdf', $length, '$content', 1
			)";
	
	if (! mysql_query($sql)) {
		logError(mysql_error() . " - $sql");
	}
	
	$documentid = mysql_insert_id();
	$memberid = getLoggedOnMemberID();
	
	$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}customerpod
			(
				customerid, documentid, poddate, 
				reference, bookingid,
				metacreateddate, metamodifieddate, 
				metacreateduserid, metamodifieduserid 
			)
			VALUES
			(
				$customerid, $documentid, NOW(), 
				'$reference', $bookingid,
				NOW(), NOW(),
				$memberid, $memberid
			)";
	
	if (! mysql_query($sql)) {
		logError($sql . " - " . mysql_error());
	}
	
	if ($bookinglink) {
		$sql = "UPDATE {$_SESSION['DB_PREFIX']}booking SET
				podsent = 'Y'
				WHERE id = $bookingid
				AND customerid = $customerid";
	
		if (! mysql_query($sql)) {
			logError(mysql_error() . " - $sql");
		}
	}
	
//	if ($customerid == 444) {
//		logError("POD inserted for $customername, Ref : $reference", false);
//	}
	
	mysql_query("COMMIT");
?>