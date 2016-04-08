<?php
	require('remittancereportlib.php');
	
	$pdf = new RemittanceReport( 'P', 'mm', 'A4', $_GET['id']);
	$pdf->Output();
?>