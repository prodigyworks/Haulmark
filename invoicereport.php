<?php
	require('invoicereportlib.php');
	
	$pdf = new InvoiceReport( 'L', 'mm', 'A4', $_GET['id']);
	$pdf->Output();
?>