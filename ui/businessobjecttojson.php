<?php
	require_once("../system-db.php");
	require_once("businessobjectjsoncall.php");
	
	start_db();	
	
	echo call(
			$_POST['classname'], 
			$_POST['methodname'], 
			$_POST['args']
		);
?>