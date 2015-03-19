<?php
	require('system-db.php');
	require('pdfreport.php');
	
	$where = "";
	
	function newPage($pdf) {
		$pdf->AddPage();
		
		$pdf->Image("images/logo.png", 170.6, 1);
//		$pdf->Image("images/footer.png", 54, 280);
		$pdf->addHeading( 15, 13, "Detail : ", "Holidays Exception Report");
	    $pdf->SetFont('Arial','', 6);
			
		$cols=array( "Staff ID"    => 23,
		             "User name"  => 46,
		             "Payroll"  => 13,
		             "Team"  => 33,
		             "Exception"  => 35,
		             "Entitlement" => 20,
		             "Taken / Booked" => 20);
	
		$pdf->addCols( 20, $cols);
		$cols=array( "Staff ID"    => "L",
		             "User name"  => "L",
		             "Payroll"  => "L",
		             "Team"  => "L",
		             "Exception"  => "L",
		             "Entitlement" => "L",
		             "Taken / Booked" => "L");
		$pdf->addLineFormat( $cols);
		
		return 29;
	}
			
	$pdf = new PDFReport( 'P', 'mm', 'A4' );
	$y = newPage($pdf);

	$sql = "SELECT A.member_id, A.login, A.firstname, A.lastname, A.prorataholidayentitlement, " .
			"SUM(daystaken) AS daystaken " .
			"FROM {$_SESSION['DB_PREFIX']}members A " .
			"INNER JOIN {$_SESSION['DB_PREFIX']}holiday B " .
			"ON B.memberid = A.member_id " .
			"WHERE YEAR(B.startdate) = YEAR(NOW()) " .
			"AND A.status = 'Y' " .
			"GROUP BY A.member_id, A.login, A.firstname, A.lastname, A.prorataholidayentitlement " .
			"HAVING SUM(daystaken) != A.prorataholidayentitlement " .
			"ORDER BY A.firstname, A.lastname";
	$result = mysql_query($sql);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$type = " ";
			
			if ($member['prorataholidayentitlement'] < $member['daystaken']) {
				$type = "Booked too many";
				
			} else if ($member['daystaken'] < 15) {
				$type = "Booked too few";
				
			}
			
//			sendInternalUserMessage($member['member_id'], "Holiday exception alert", "<p>Your holiday entitlement does not match the number of days booked this year</p><p>Reason : $type</p>");
//			smtpmailer("hr@pestokill.co.uk", "cpd@pestokill.co.uk", "Pestokill Administration", "Holiday exception alert", getEmailHeader() . "<h4>Dear " . $member['firstname'] . "<p>Your holiday entitlement does not match the number of days booked this year</p><p>Reason : $type</p>". getEmailFooter(), $attachments);
			
			$line=array( "Staff ID"    => $member['staffnumber'],
			             "User name"  => $member['firstname'] . " " . $member['lastname'],
			             "Payroll"     => "-",
			             "Team"     => "-",
			             "Exception"     => $type,
			             "Entitlement" => $member['prorataholidayentitlement'],
			             "Taken / Booked" => $member['daystaken']
			             );
			             
			$size = $pdf->addLine( $y, $line );
			$y += $size;
			
			if ($y > 260) {
				$y = newPage($pdf);
			}
		}
		
	} else {
		logError($sql . " - " . mysql_error());
	}
	
	$pdf->Output();
?>