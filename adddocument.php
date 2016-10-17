<?php
	require_once('documentfunctions.php');

	uploadDocuments(
			$_GET['id'],
			$_GET['primaryidname'],
			$_GET['tablename']
		);
	
	mysql_query("COMMIT");
		
	if (isset($_GET['refer'])) {
	  	header("location: " . base64_decode($_GET['refer']));
		
	} else {
	  	header("location: " . $_SERVER['HTTP_REFERER']);
	}	
?>