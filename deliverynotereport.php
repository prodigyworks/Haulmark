<?php
	require_once('deliverynotereportlib.php');
	require_once('deliverynotereportlib2.php');
	
	start_db();
	
	$sql = "SELECT IF (startvisittype IS NULL, 'Y', 'N') AS type 
			FROM {$_SESSION['DB_PREFIX']}booking
			WHERE id = {$_GET['id']}";

	$result = mysql_query($sql);
	
	if (! $result) {
		SQLError($sql);
	}
	
	while (($member = mysql_fetch_assoc($result))) {
		if ($member['type'] == "Y") {
			$pdf = new DeliveryNoteReport( 'P', 'mm', 'A4', array($_GET['id']));
			
		} else {
			$pdf = new DeliveryNoteReport2( 'P', 'mm', 'A4', array($_GET['id']));
		}
	}
	
	$pdf->Output();
?>