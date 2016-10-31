<?php
	require_once('system-db.php');
	require_once('pdfreport.php');
	require_once("simple_html_dom.php");
	
	class MaintenanceReport extends PDFReport {
		private $headermember = null;
		private $dateFrom;
		private $dateTo;
		public $headertitle;
		
		function AddPage($orientation='', $size='') {
			parent::AddPage($orientation, $size);
			
			$this->Image("images/logomain2.png", 244.6, 6);
			
			$this->addText( 10, 13, $this->headertitle . " Maintenance Report", 11, 'B');
			$this->addText( 10, 18, "For the period " . $this->dateFrom . " to " . $this->dateTo, 8, 'B');
			
		    $this->SetFont('Arial','', 6);
				
			$this->addCols(
					28, 
					array( 
				            $this->headertitle	=> 30,
							"Start Date"  		=> 24,
							"End Date"  		=> 23,
							"Reason"  			=> 43,
							"Work carried out"	=> 138,
							"Total Cost"  		=> 19
					)
				);
				
			$this->addLineFormat( 
					array( 
				            $this->headertitle  => "L",
							"Start Date"  		=> "L",
				            "End Date"  		=> "L",
				            "Reason"  			=> "L",
							"Work carried out"	=> "L",
							"Total Cost"  		=> "R"
					)
				);
			
			$this->SetY(36);
		}

		function __construct($orientation, $metric, $size, $dateFrom, $dateTo) {
			start_db();
			
			$this->headertitle = "Vehicle";
			$this->dateFrom = $dateFrom;
			$this->dateTo = $dateTo;
			
			$dateFrom = convertStringToDate($dateFrom);
			$dateTo = convertStringToDate($dateTo);
				
	        parent::__construct($orientation, $metric, $size);
	        
	        $this->AddPage();
			
	        if (count($_POST['vehiclereason']) > 0) {
				try {
					$totalcost = 0;
					$reasoncodes = ArrayToInClause($_POST['vehiclereason']);
					
					$sql = "SELECT A.*, 
							DATE_FORMAT(A.startdate, '%d/%m/%Y') AS startdate,
							DATE_FORMAT(A.enddate, '%d/%m/%Y') AS enddate,
							B.name,
							C.registration
							FROM  {$_SESSION['DB_PREFIX']}vehicleunavailability A
						    INNER JOIN  {$_SESSION['DB_PREFIX']}vehicleunavailabilityreasons B
						    ON B.id = A.reasonid
						    INNER JOIN  {$_SESSION['DB_PREFIX']}vehicle C
						    ON C.id = A.vehicleid
						    WHERE DATE(A.enddate) >= '$dateFrom'
						    AND DATE(A.enddate) <= '$dateTo'
						    AND A.reasonid IN ($reasoncodes)
						    ORDER BY A.enddate";
					$result = mysql_query($sql);
					
					if ($result) {
						while (($member = mysql_fetch_assoc($result))) {
							if ($this->GetY() > 175) {
								$this->AddPage();
							}
							
							$totalcost += $member['totalcost'];
							
							$this->setY(
									$this->GetY() +
									$this->addLine(
											$this->getY(), 
											array( 
												$this->headertitle	=> $member['registration'],
									            "Start Date"  		=> $member['startdate'],
									            "End Date"  		=> $member['enddate'],
									            "Reason"  			=> $member['name'],
												"Work carried out"	=> $member['workcarriedout'],
												"Total Cost"  		=> "£ " . number_format($member['totalcost'], 2)
											)
										)
								);
						}
						
					} else {
						logError("$sql - " . mysql_error());
					}
					
					$this->Line(10, $this->GetY(), 287, $this->GetY());
					$this->SetFont('Arial','B', 6);
					
					$this->setY(
							$this->GetY() +
							$this->addLine(
									$this->getY() + 2, 
									array( 
										"Total Cost"  		=> "£ " . number_format($totalcost, 2)
									)
								)
						);
			
				} catch (Exception $e) {
					logError($e->getMessage());
				}
	        }
			
			$this->headertitle = "Trailer";
	        $this->AddPage();
			
	        if (count($_POST['trailerreason']) > 0) {
				try {
					$totalcost = 0;
					$reasoncodes = ArrayToInClause($_POST['trailerreason']);
					
					$sql = "SELECT A.*, 
							DATE_FORMAT(A.startdate, '%d/%m/%Y') AS startdate,
							DATE_FORMAT(A.enddate, '%d/%m/%Y') AS enddate,
							B.name,
							C.registration
							FROM  {$_SESSION['DB_PREFIX']}trailerunavailability A
						    INNER JOIN  {$_SESSION['DB_PREFIX']}trailerunavailabilityreasons B
						    ON B.id = A.reasonid
						    INNER JOIN  {$_SESSION['DB_PREFIX']}trailer C
						    ON C.id = A.trailerid
						    WHERE DATE(A.enddate) >= '$dateFrom'
						    AND DATE(A.enddate) <= '$dateTo'
						    AND A.reasonid IN ($reasoncodes)
						    ORDER BY A.enddate";
					$result = mysql_query($sql);
					
					if ($result) {
						while (($member = mysql_fetch_assoc($result))) {
							if ($this->GetY() > 175) {
								$this->AddPage();
							}
							
							$totalcost += $member['totalcost'];
							
							$this->setY(
									$this->GetY() +
									$this->addLine(
											$this->getY(), 
											array( 
												$this->headertitle	=> $member['registration'],
									            "Start Date"  		=> $member['startdate'],
									            "End Date"  		=> $member['enddate'],
									            "Reason"  			=> $member['name'],
												"Work carried out"	=> $member['workcarriedout'],
												"Total Cost"  		=> "£ " . number_format($member['totalcost'], 2)
											)
										)
								);
						}
						
					} else {
						logError("$sql - " . mysql_error());
					}
					
					$this->Line(10, $this->GetY(), 287, $this->GetY());
					$this->SetFont('Arial','B', 6);
					
					$this->setY(
							$this->GetY() +
							$this->addLine(
									$this->getY() + 2, 
									array( 
										"Total Cost"  		=> "£ " . number_format($totalcost, 2)
									)
								)
						);
			
				} catch (Exception $e) {
					logError($e->getMessage());
				}
	        }
			
			$this->AliasNbPages();
		}
	}
	
	$pdf = new MaintenanceReport( 
			'L', 
			'mm', 
			'A4', 
			$_POST['fromdate'], 
			$_POST['todate']
		);
	$pdf->Output();
?>