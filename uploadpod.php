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
	$reference = substr($file, 0, lastIndexOf($file, "."));
	
    $tmpName  = $_FILES['content']['tmp_name'];  
    $content = "";
       
    // Read the file 
    $fp = fopen($tmpName, 'rb');
      
	while (!feof($fp)) {
		$content .= fread($fp, 192);
	}
      
    fclose($fp);
	
    $content = mysql_escape_string($content);
       
	
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
	
	$length = strlen($content);
	$content = gzcompress($content, 9);
	
	$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}documents
			(
				name, filename, mimetype, size, image, compressed
			)
			VALUES
			(
				'$file', '$file', 'application/pdf', $length, '$content', 1
			)";
	
	if (! mysql_query($sql)) {
		logError($sql . " - " . mysql_error());
	}
	
	$documentid = mysql_insert_id();
	$memberid = getLoggedOnMemberID();
	
	$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}customerpod
			(
				customerid, documentid, poddate, reference,
				metacreateddate, metamodifieddate, 
				metacreateduserid, metamodifieduserid 
			)
			VALUES
			(
				$customerid, $documentid, NOW(), '$reference',
				NOW(), NOW(),
				$memberid, $memberid
			)";
	
	if (! mysql_query($sql)) {
		logError($sql . " - " . mysql_error());
	}
	
	echo "POD inserted";
	
	mysql_query("COMMIT");
?>