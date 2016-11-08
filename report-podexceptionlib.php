<?php
	require_once('system-db.php');
	require_once('pdfreport.php');
	require_once("simple_html_dom.php");
	
	class ExceptionReport extends PDFReport {
		private $headermember = null;
		private $dateFrom;
		private $dateTo;
		
		function AddPage($orientation='', $size='') {
			parent::AddPage($orientation, $size);
			
			$this->Image("images/logomain2.png", 260.6, 6);
			
			$this->addText( 10, 13, "POD Exception Report", 11, 'B');
			$this->addText( 10, 18, "For the period " . $this->dateFrom . " to " . $this->dateTo, 8, 'B');
			
		    $this->SetFont('Arial','', 6);
				
			$this->addCols(
					28, 
					array( 
							"Booking Number"	=> 24,
				            "Date"  			=> 20,
				            "Company Name"  	=> 44,
				            "Journey"  			=> 129,
				            "Pallets"  			=> 20,
				            "Weight (Kg)"  			=> 20,
				            "Revenue"  			=> 20
						)
				);
				
			$this->addLineFormat( 
					array( 
							"Booking Number"	=> "L",
				            "Date"  			=> "L",
				            "Company Name"  	=> "L",
				            "Journey"  			=> "L",
				            "Pallets"  			=> "R",
				            "Weight (Kg)"  			=> "R",
				            "Revenue"  			=> "R"
						)
				);
			
			$this->SetY(36);
		}

		function __construct($orientation, $metric, $size, $dateFrom, $dateTo, $customerid) {
			start_db();
			
			$this->dateFrom = $dateFrom;
			$this->dateTo = $dateTo;
			
			$dateFrom = convertStringToDate($dateFrom);
			$dateTo = convertStringToDate($dateTo);
				
	        parent::__construct($orientation, $metric, $size);
	        
	        $this->AddPage();
			
			try {
				$and = "";
				
				if ($customerid != 0) {
					$and = "AND A.customerid = $customerid";
				}
				
				$sql = "SELECT A.*, DATE_FORMAT(A.startdatetime, '%d/%m/%Y') AS startdatetime,
						B.name AS customername
						FROM  {$_SESSION['DB_PREFIX']}booking A
					    INNER JOIN  {$_SESSION['DB_PREFIX']}customer B
					    ON B.id = A.customerid
					    WHERE DATE(A.startdatetime) >= '$dateFrom'
					    AND DATE(A.startdatetime) <= '$dateTo'
					    AND (A.podsent IS NULL OR A.podsent = 'N')
					    AND A.statusid >= 7
					    $and
					    ORDER BY B.name, A.id DESC";
				$result = mysql_query($sql);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($this->GetY() > 175) {
							$this->AddPage();
						}
						
						$this->setY(
								$this->GetY() +
								$this->addLine(
										$this->getY(), 
										array( 
												"Booking Number"	=> getBookingReference($member['id']),
									            "Date"  			=> $member['startdatetime'],
									            "Company Name"  	=> $member['customername'],
									            "Journey"  			=> $member['legsummary'],
									            "Pallets"  			=> $member['pallets'],
									            "Weight (Kg)"  		=> $member['weight'],
									            "Revenue"  			=> "£ " . $member['charge']
											)
									)
							);
					}
					
				} else {
					logError($sql . " - " . mysql_error());
				}
				
			} catch (Exception $e) {
				logError($e->getMessage());
			}
			
			$this->AliasNbPages();
		}
	}
	
	$pdf = new ExceptionReport( 
			'L', 
			'mm', 
			'A4', 
			$_POST['fromdate'], 
			$_POST['todate'],
			$_POST['customerid']
		);
	$pdf->Output();
?>