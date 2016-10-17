<?php
	require_once('system-db.php');
	
	start_db();

	$id = $_GET['id'];
	
	if(!isset($id)){
	     logError("Please select your image!");
	     
	} else {
		$query = mysql_query(
				"SELECT mimetype, name, image 
				 FROM {$_SESSION['DB_PREFIX']}images 
				 WHERE id = $id"
			);
		$row = mysql_fetch_array($query);
		
		$content = $row['image'];
		$mimetype = $row['mimetype'];
		
		header("Content-type: $mimetype");

	    echo $content;
	}
?> 