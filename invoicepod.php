<?php
require_once("system-db.php");

function successPODEmail($id) {
	$sql = "UPDATE {$_SESSION['DB_PREFIX']}POD SET 
			emaileddate = NOW(),
			emailed = 'Y',
			emailfailedreason = null
			WHERE id = $id";
	
	if (! mysql_query($sql)) {
		logError("Error updating POD: $sql - " . mysql_error());
	}
}

function failurePODEmail($id, $reason) {
	$emailreason = mysql_escape_string($reason);
	$sql = "UPDATE {$_SESSION['DB_PREFIX']}POD SET 
			emaileddate = NOW(),
			emailed = 'N',
			emailfailedreason = '$emailreason'
			WHERE id = $id";
	
	if (! mysql_query($sql)) {
		logError("Error updating POD: $sql - " . mysql_error());
	}
	
	return new Exception($reason);
}

function PODEmail($id) {
	start_db();
	
	$sql = "SELECT B.email, B.name 
			FROM {$_SESSION['DB_PREFIX']}POD A
			INNER JOIN {$_SESSION['DB_PREFIX']}customer B
			ON B.id = A.customerid
			WHERE A.id = $id";
	$result = mysql_query($sql);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$email = $member['email'];
			$name = $member['name'];
			
			if ($email == null || $email == "") {
				throw failurePODEmail($id, "Email not specified for customer [$name]");
			}
			
			$filename = "uploads/$POD.pdf";
			$POD = "INV-" . sprintf("%06d", $id);
			
			unlink($filename);
			
			$pdf = new PODReport( 'L', 'mm', 'A4', $id);
			$pdf->Output($filename);
			
			try {
				smtpmailer(
						$email, 
						"info@allegrotransport.co.uk", 
						"Allegro Transport Limited", 
						"POD : $POD", 
						"Please find the attached PDF for POD $POD.", 
						array($filename)
					);
					
			} catch (Exception $e) {
				throw failurePODEmail($id, $e->getMessage());
			}
		}
		
	} else {
		throw failurePODEmail($id, mysql_error());
	}
	
	successPODEmail($id);
}
?>