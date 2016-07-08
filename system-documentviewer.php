<?php
	require_once('system-db.php');
	
	start_db();
	initialise_db();

	$id = $_GET['id'];
	
	if(!isset($id)){
	     logError("Please select your document!");
	     
	} else {
		$query = mysql_query("SELECT compressed, mimetype, image, size, filename 
							  FROM {$_SESSION['DB_PREFIX']}documents 
							  WHERE id = $id");
		$row = mysql_fetch_array($query);
		$content = $row['image'];
		$filename = $row['filename'];
		
		if ($row['compressed'] == 1) {
			$content = gzuncompress($content);
		}
		
		header('Content-type: ' . $row['mimetype']);
		
	    echo $content;
	}
?> 