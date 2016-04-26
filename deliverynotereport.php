<?php
	require_once('pdfreport.php');
	require_once('system-db.php');
	
	class DeliveryNoteReport extends PDFReport {
		var $y = 35;
		var $member;
		
		function newPage() {
			global $y, $member;
			
			$this->addPage();
			
			$this->SetDrawColor(180, 180, 180);
			$this->Image("images/logomain.png", 10, 6);
			$this->Image("images/report-footer.png", 55, 280);
				
			$y = $this->GetY() + 38;
			$y = $this->addText(80, $y, "Delivery Note", 15, 8, 'B', 90);
			
			$this->Line(10, 48, 200, 48);
			$this->Line(10, 56, 200, 56);
				
			$y = 10;
				
			$y = $this->addText(150, $y, "Allegro Transport", 10, 4, 'B', 80);
			$y = $this->addText(150, $y, "Cotes Park Industrial Estate", 10, 4, '', 80);
			$y = $this->addText(150, $y, "Birchwood Way", 10, 4, '', 80);
			$y = $this->addText(150, $y, "Alfreton", 10, 4, '', 80);
			$y = $this->addText(150, $y, "Derbyshire", 10, 4, '', 80);
			$y = $this->addText(150, $y, "DE55 4QQ", 10, 4, '', 80);
			$y = $this->addText(150, $y, "Tel : 01773 541771", 10, 4, '', 80);
			$y = $this->addText(150, $y, "Fax : 01773 541774", 10, 4, '', 80);
			
				
			$y = 60;
			
			if ($member['customername'] != "") $y = $this->addText(15, $y, $member['customername'], 10, 4, 'B', 80);
			if ($member['street'] != "") $y = $this->addText(15, $y, $member['street'], 10, 4, '', 80);
			if ($member['town'] != "") $y = $this->addText(15, $y, $member['town'], 10, 4, '', 80);
			if ($member['city'] != "") $y = $this->addText(15, $y, $member['city'], 10, 4, '', 80);
			if ($member['county'] != "") $y = $this->addText(15, $y, $member['county'], 10, 4, '', 80);
			if ($member['postcode'] != "") $y = $this->addText(15, $y, $member['postcode'], 10, 4, '', 80);
			if ($member['telephone'] != "") $y = $this->addText(15, $y, "Tel : " . $member['telephone'], 10, 4, '', 80);
			if ($member['fax'] != "") $y = $this->addText(15, $y, "Fax : " . $member['fax'], 10, 4, '', 80);
			
			$y += 10;
			$this->Line(10, $y - 5, 200, $y - 5);
			
			$this->addText(15, $y, "Job Ticket No", 10, 4.5, 'B', 80);
			$y = $this->addText(60, $y, $member['id'], 10, 4.5, '', 80);
				
			$this->addText(15, $y, "Driver", 10, 4.5, 'B', 80);
			$y = $this->addText(60, $y, $member['drivername'], 10, 4.5, '', 80);
			
			$this->addText(15, $y, "Vehicle", 10, 4.5, 'B', 80);
			$y = $this->addText(60, $y, $member['registration'], 10, 4.5, '', 80);
			
			$this->addText(15, $y, "Trailer", 10, 4.5, 'B', 80);
			$y = $this->addText(60, $y, $member['trailername'], 10, 4.5, '', 80);
			
			$this->addText(15, $y, "Customer Order Number", 10, 4.5, 'B', 80);
			$y = $this->addText(60, $y, $member['ordernumber'], 10, 4.5, '', 80);
			
			$this->addText(15, $y, "Date Issued", 10, 4.5, 'B', 80);
			$y = $this->addText(60, $y, $member['metacreateddate'], 10, 4.5, '', 80);

			$this->Line(10, $y + 5, 200, $y + 5);
				
			$top = $y;
			$y = 180;
			$this->Line(10, $y - 2, 200, $y - 2);
				
			$this->addText(15, $y, "Weight", 10, 4.5, 'B', 80);
			$y = $this->addText(60, $y, $member['weight'], 10, 4.5, '', 80);
				
			$this->addText(15, $y, "Volume", 10, 4.5, 'B', 80);
			$y = $this->addText(60, $y, $member['capacity'], 10, 4.5, '', 80);

			$this->addText(15, $y, "Number Of Pallets", 10, 4.5, 'B', 80);
			$y = $this->addText(60, $y, $member['pallets'], 10, 4.5, '', 80);

			$this->addText(15, $y, "Number Of Items", 10, 4.5, 'B', 80);
			$y = $this->addText(60, $y, $member['items'], 10, 4.5, '', 80);

			$this->addText(15, $y, "Special Instructions", 10, 4.5, 'B', 80);
			$this->WriteHTML(60, $y - 2, $member['notes']);
			$y = 250;
			

			$this->addText(10, 260, "Delivery Signed In Good Condition", 10, 4.5, 'B', 80);
			$this->Line(10, $y - 2, 200, $y - 2);
			$this->Line(10, $y - 2, 10, 48);
			$this->Line(200, $y - 2, 200, 48);
			$this->SetDash(0.5, 1); //5mm on, 5mm off
			$this->Line(75, 263.5, 150, 263.5);
			$this->addText(160, 260, "Date             /      /  ", 10, 4.5, 'B', 80);
			$this->Line(175, 263.5, 200, 263.5);
				
			$this->addText(10, 270, "ALL GOODS CARRIED UNDER R.H.A. CONDITIONS OF CARRIAGE", 7, 4.5, '', 120);
				
			$y = $top + 10;
		}
		
		function __construct($orientation, $metric, $size, $id) {
	        parent::__construct($orientation, $metric, $size);
			
			global $y, $member;
			
			$sql = "SELECT A.*, 
					DATE_FORMAT(A.startdatetime, '%d/%m/%Y') AS startdate, 
					DATE_FORMAT(A.startdatetime, '%H:%i') AS starttime, 
					DATE_FORMAT(A.enddatetime, '%d/%m/%Y') AS enddate, 
					DATE_FORMAT(A.enddatetime, '%H:%i') AS endtime, 
					DATE_FORMAT(A.metacreateddate, '%d/%m/%Y') AS metacreateddate, 
					B.registration AS trailername, C.capacity, C.registration, D.name AS drivername, D.telephone,
					E.name AS customername, E.street, E.city, E.town, E.postcode, E.county, E.postcode, E.telephone, E.fax 
					FROM {$_SESSION['DB_PREFIX']}booking A 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}trailer B 
					ON B.id = A.trailerid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}vehicle C 
					ON C.id = A.vehicleid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}driver D 
					ON D.id = A.driverid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}customer E 
					ON E.id = A.customerid 
					WHERE A.id = $id";
			$result = mysql_query($sql);
			
			if ($result) {
				$first = true;
				
				while (($member = mysql_fetch_assoc($result))) {
					if ($first) {
						$this->newPage();
						$first = false;
					}
					
					$bookingid = $member['id'];
						
					$sql = "SELECT A.*,
							DATE_FORMAT(A.departuretime, '%d/%m/%Y') AS startdate,
							DATE_FORMAT(A.departuretime, '%H:%i') AS starttime
							FROM {$_SESSION['DB_PREFIX']}bookingleg A
							WHERE A.bookingid = $id 
							ORDER BY A.id";
					$itemresult = mysql_query($sql);
										
					if ($itemresult) {
						while (($itemmember = mysql_fetch_assoc($itemresult))) {
							$startdate = $itemmember['startdate'];
							$starttime = $itemmember['starttime'];
							$fromplace = $itemmember['place'];
						}
					
					} else {
						logError($sql . " - " . mysql_error());
					}
					
					$this->addText(15, $y, "Collection", 10, 4.5, 'B', 80);
					$y = $this->addText(60, $y, $fromplace, 10, 4.5, '', 80);
					$this->addText(15, $y, "Date", 10, 4.5, 'B', 80);
					$y = $this->addText(60, $y, $startdate, 10, 4.5, '', 80);
					$this->addText(15, $y, "Time", 10, 4.5, 'B', 80);
					$y = $this->addText(60, $y, $starttime, 10, 4.5, '', 80) + 4;
					
 					$this->addText(15, $y, "Delivery", 10, 4.5, 'B', 80);
 					$y = $this->addText(60, $y, $member['toplace'], 10, 4.5, '', 80);
					$this->addText(15, $y, "Date", 10, 4.5, 'B', 80);
					$y = $this->addText(60, $y, $member['enddate'], 10, 4.5, '', 80);
					$this->addText(15, $y, "Time", 10, 4.5, 'B', 80);
					$y = $this->addText(60, $y, $member['endtime'], 10, 4.5, '', 80);
				}
				
			} else {
				logError($sql . " - " . mysql_error());
			}
		}
	}
	
	$pdf = new DeliveryNoteReport( 'P', 'mm', 'A4', $_GET['id']);
	$pdf->Output();
?>