<?php
	require_once('deliverynotereportlib.php');
	
	$pdf = new DeliveryNoteReport( 'P', 'mm', 'A4', array($_GET['id']));
	$pdf->Output();
?>