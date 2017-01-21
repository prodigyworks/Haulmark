<?php
	require_once("../system-db.php");
	require_once("businessobjecttojsoncall.php");
	
	start_db();	
	
	header("Content-type: application/json");
	
	echo businessObjectToJSon(
			$_POST['classname'], 
			$_POST['methodname'], 
			$_POST['args']
		);
?>