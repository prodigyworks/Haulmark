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
			
			$this->addText( 10, 13, "Pre-invoice Report", 11, 'B');
			$this->addText( 10, 18, "For the period " . $this->dateFrom . " to " . $this->dateTo, 8, 'B');
			
		    $this->SetFont('Arial','', 6);
				
			$this->addCols(
					28, 
					array( 
							"Booking Number"	=> 24,
							"Order Number"		=> 35,
							"Date"  			=> 16,
				            "Company Name"  	=> 44,
				            "Journey"  			=> 98,
				            "Pallets"  			=> 15,
				            "Weight (Kg)"  		=> 25,
				            "Revenue"  			=> 20
						)
				);
				
			$this->addLineFormat( 
					array( 
							"Booking Number"	=> "L",
							"Order Number"		=> "L",
							"Date"  			=> "L",
				            "Company Name"  	=> "L",
				            "Journey"  			=> "L",
				            "Pallets"  			=> "R",
				            "Weight (Kg)"  		=> "R",
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
				$cumulativetotal = 0;
				$grouptotal = 0;
				$prevcustomername = "";
				
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
					    AND B.selfbilledinvoices != 'N'
					    AND A.statusid = 7
					    AND A.charge > 0
					    $and
					    ORDER BY B.name, A.id DESC";
				$result = mysql_query($sql);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($prevcustomername != "" && $prevcustomername != $member['customername']) {
							$this->Line(10, $this->GetY(), 287, $this->GetY());
				    		$this->SetFont('Arial','B', 6);
							$this->setY(
									$this->GetY() + 4 +
									$this->addLine(
											$this->getY() + 2, 
											array(
													"Weight (Kg)" 	=> "Customer Total :",
													"Revenue" 		=> "£ " . number_format($grouptotal, 2)
												)
										)
								);
				    		$this->SetFont('Arial','', 6);
				    		
				    		$grouptotal = 0;
						}
						
						if ($this->GetY() > 175) {
							$this->AddPage();
						}
						
						$this->setY(
								$this->GetY() +
								$this->addLine(
										$this->getY(), 
										array( 
												"Booking Number"	=> getBookingReference($member['id']),
									            "Order Number"      => $member['ordernumber'],
												"Date"  			=> $member['startdatetime'],
									            "Company Name"  	=> $member['customername'],
									            "Journey"  			=> $member['legsummary'],
									            "Pallets"  			=> $member['pallets'],
									            "Weight (Kg)"  		=> $member['weight'],
									            "Revenue"  			=> "£ " . $member['charge']
											)
									)
							);
						             
						$prevcustomername = $member['customername'];
					    $cumulativetotal += $member['charge'];
					    $grouptotal += $member['charge'];
					}
					
					if ($prevcustomername != "") {
						$this->Line(10, $this->GetY(), 287, $this->GetY());
					
						$this->Line(10, $this->GetY(), 287, $this->GetY());
			    		$this->SetFont('Arial','B', 6);
						$this->setY(
								$this->GetY() + 4 +
								$this->addLine(
										$this->getY() + 2, 
										array(
												"Weight (Kg)" 	=> "Customer Total :",
												"Revenue" 		=> "£ " . number_format($grouptotal, 2)
											)
									)
							);
					}
						
					$this->Line(10, $this->GetY(), 287, $this->GetY());
					$this->SetFont('Arial','B', 6);
					$this->setY(
							$this->GetY() + 4 +
							$this->addLine(
									$this->getY() + 2, 
									array(
											"Weight (Kg)" 	=> "Total :",
											"Revenue" 		=> "£ " . number_format($cumulativetotal, 2)
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
			$_POST['todate'],
			$_POST['customerid']
		);
	$pdf->Output();
?>