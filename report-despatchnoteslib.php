<?php
	require_once('deliverynotereportlib.php');
	
	start_db();

	$bookings = array();
	$fromdate = convertStringToDate($_POST['fromdate']);
	$todate = convertStringToDate($_POST['todate']);
	$sql = "SELECT id 
			FROM {$_SESSION['DB_PREFIX']}booking 
			WHERE (DATE(startdatetime) >= '$fromdate') 
			AND   (DATE(startdatetime) <= '$todate')";
	
	$result = mysql_query($sql);
	
	if (! $result) {
		logError("$sql - " . mysql_error());
	}
	
	while (($member = mysql_fetch_assoc($result))) {
		array_push($bookings, $member['id']);
	}
	
	if (count($bookings) > 0) {
		$pdf = new DeliveryNoteReport( 'P', 'mm', 'A4', $bookings);
		$pdf->Output();
	} else {
		require_once("system-header.php");
?>
		<h1>No despatch notes found.</h1>
<?php
		require_once("system-header.php");
	}
?>