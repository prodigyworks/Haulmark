<?php
	require_once('documentfunctions.php');

	clearSessionDocuments();
	
	mysql_query("COMMIT");
?>
