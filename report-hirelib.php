<?php
	require_once('system-db.php');
	require_once('pdfreport.php');
	require_once("simple_html_dom.php");
	
	class InvoiceReport extends PDFReport {
		private $headermember = null;
		private $dateFrom;
		private $dateTo;
		
		function AddPage($orientation='', $size='') {
			parent::AddPage($orientation, $size);
			
			$this->Image("images/logomain2.png", 260.6, 6);
			
			$this->addText( 10, 13, "Vehicle Hire Report", 11, 'B');
			$this->addText( 10, 18, "For the period " . $this->dateFrom . " to " . $this->dateTo, 8, 'B');
			
		    $this->SetFont('Arial','', 6);
				
			$this->addCols(
					28, 
					array( 
							"Booking"		=> 20,
							"Agency"		=> 41,
				            "Vehicle"  		=> 20,
				            "Registration"	=> 20,
							"Driver"  		=> 30,
							"Date"  		=> 17,
				            "Load"  		=> 29,
							"Journey"  		=> 70,
				            "Hours"  		=> 15,
				            "Cost"  		=> 15,
					)
				);
				
			$this->addLineFormat( 
					array( 
							"Booking"	=> "L",
							"Agency"	=> "L",
				            "Date"  	=> "L",
				            "Load"  	=> "L",
				            "Vehicle"  	=> "L",
				            "Registration"	=> "L",
							"Journey"  	=> "L",
				            "Driver"  	=> "L",
				            "Hours"  	=> "R",
				            "Cost"  	=> "R",
					)
				);
			
			$this->SetY(36);
		}

		function __construct($orientation, $metric, $size, $dateFrom, $dateTo) {
			start_db();
			
			$this->dateFrom = $dateFrom;
			$this->dateTo = $dateTo;
			
			$dateFrom = convertStringToDate($dateFrom);
			$dateTo = convertStringToDate($dateTo);
				
	        parent::__construct($orientation, $metric, $size);
	        
	        $this->AddPage();
			
			try {
				$totalhours = 0;
				$totalcost = 0;
				
				$sql = "SELECT A.*, DATE_FORMAT(A.startdatetime, '%d/%m/%Y') AS startdatetime,
						time_to_sec(timediff(enddatetime, startdatetime)) AS hours,
						B.name AS drivername,
						C.name AS loadname, C.agencydayrate,
						D.description AS agencyname, D.registration,
						E.name AS suppliername,
						F.rateper24hours
						FROM  {$_SESSION['DB_PREFIX']}booking A
					    INNER JOIN  {$_SESSION['DB_PREFIX']}driver B
					    ON B.id = A.driverid
					    INNER JOIN  {$_SESSION['DB_PREFIX']}vehicletype C
					    ON C.id = A.vehicletypeid
					    INNER JOIN  {$_SESSION['DB_PREFIX']}vehicle D
					    ON D.id = A.vehicleid
					    LEFT OUTER JOIN  {$_SESSION['DB_PREFIX']}supplier E
					    ON E.id = D.supplierid
					    LEFT OUTER JOIN  {$_SESSION['DB_PREFIX']}supplierrates F
					    ON F.supplierid = E.id
					    AND F.vehicletypeid = C.id
					    WHERE DATE(A.startdatetime) >= '$dateFrom'
					    AND DATE(A.startdatetime) <= '$dateTo'
					    AND A.statusid >= 7
					    AND D.subcontractor = 'Y'
					    ORDER BY A.id";
				$result = mysql_query($sql);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($member['rateper24hours'] == null) {
							$rate = 80;
							
						} else {
							$rate = $member['rateper24hours'];
						}
						
						if ($this->GetY() > 175) {
							$this->AddPage();
						}
						
						$cost = intval(($member['hours'] / 3600) / 24) * $rate;
						
						if (intval(($member['hours'] / 3600) % 24) != 0) {
							$cost += $rate;
						}
						
						$this->setY(
								$this->GetY() +
								$this->addLine(
										$this->getY(), 
										array( 
											"Booking"	=> getBookingReference($member['id']),
											"Agency"	=> $member['agencyname'],
								            "Date"  	=> $member['startdatetime'],
								            "Load"  	=> $member['loadname'],
								            "Vehicle"  	=> $member['registration'],
								            "Registration"	=> $member['agencyvehicleregistration'],
											"Journey"  	=> $member['legsummary'],
								            "Driver"  	=> $member['drivername'],
								            "Hours"  	=> $member['duration'],
											"Cost"		=> "£ ". number_format($cost, 2)
										)
									)
							);
							
						$totalhours += $member['duration'];
						$totalcost += $cost;
					}
					
					$this->Line(10, $this->GetY(), 287, $this->GetY());
					$this->SetFont('Arial','B', 6);
					$this->setY(
							$this->GetY() +
							$this->addLine(
									$this->getY() + 1, 
									array( 
							            "Journey"  	=> "Total",
							            "Hours"  	=> number_format($totalhours, 2),
							            "Cost"  	=> "£ ". number_format($totalcost, 2)
									)
								)
						);
										
				} else {
					logError($sql . " - " . mysql_error());
				}
				
			} catch (Exception $e) {
				logError($e->getMessage());
			}
			
			$this->AliasNbPages();
		}
	}
	
	$pdf = new InvoiceReport( 
			'L', 
			'mm', 
			'A4', 
			$_POST['fromdate'], 
			$_POST['todate']
		);
	$pdf->Output();
?>