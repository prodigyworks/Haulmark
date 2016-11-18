<?php
require_once("system-db.php");
require_once("invoicereportlib.php");

function successInvoiceEmail($id) {
	$sql = "UPDATE {$_SESSION['DB_PREFIX']}invoice SET 
			emaileddate = NOW(),
			emailed = 'Y',
			emailfailedreason = null
			WHERE id = $id";
	
	if (! mysql_query($sql)) {
		logError("Error updating invoice: $sql - " . mysql_error());
	}
}

function failureInvoiceEmail($id, $reason) {
	$emailreason = mysql_escape_string($reason);
	$sql = "UPDATE {$_SESSION['DB_PREFIX']}invoice SET 
			emaileddate = NOW(),
			emailed = 'N',
			emailfailedreason = '$emailreason'
			WHERE id = $id";
	
	if (! mysql_query($sql)) {
		logError("Error updating invoice: $sql - " . mysql_error());
	}
	
	return new Exception($reason);
}

function invoiceEmail($id) {
	start_db();
	
	$sql = "SELECT B.email, B.name 
			FROM {$_SESSION['DB_PREFIX']}invoice A
			INNER JOIN {$_SESSION['DB_PREFIX']}customer B
			ON B.id = A.customerid
			WHERE A.id = $id";
	$result = mysql_query($sql);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$email = $member['email'];
			$name = $member['name'];
			
			if ($email == null || $email == "") {
				throw failureInvoiceEmail($id, "Email not specified for customer [$name]");
			}
			
			$filename = "uploads/$invoice.pdf";
			$invoice = "INV-" . sprintf("%06d", $id);
			
			unlink($filename);
			
			$pdf = new InvoiceReport( 'L', 'mm', 'A4', $id);
			$pdf->Output($filename);
			
			try {
				smtpmailer(
						$email, 
						"admin@haulageplanner.co.uk", 
						"Allegro Transport Limited", 
						"Invoice : $invoice", 
						"Please find the attached PDF for invoice $invoice.", 
						array($filename)
					);
					
			} catch (Exception $e) {
				throw failureInvoiceEmail($id, $e->getMessage());
			}
		}
		
	} else {
		throw failureInvoiceEmail($id, mysql_error());
	}
	
	successInvoiceEmail($id);
}
?>