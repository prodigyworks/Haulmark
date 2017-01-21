<?php
	require_once('system-db.php');
	require_once('pdfreport.php');
	require_once("simple_html_dom.php");
	
	class POReport extends PDFReport {
		private $headermember = null;
		private $dateFrom;
		private $dateTo;
		
		function AddPage($orientation='', $size='') {
			parent::AddPage($orientation, $size);
			
			$this->Image("images/logomain2.png", 170.6, 6);
			
			$this->addText( 10, 13, "Purchase Orders", 11, 'B');
			$this->addText( 10, 18, "For the period " . $this->dateFrom . " to " . $this->dateTo, 8, 'B');
			
		    $this->SetFont('Arial','', 6);
				
			$this->addCols(
					28, 
					array( 
							"Purchase Order"	=> 24,
				            "Date"  			=> 20,
				            "Supplier"  		=> 44,
				            "Revision"  		=> 20,
				            "Your Order Number" => 25,
				            "Taken By"  		=> 38,
							"Cost"				=> 19
						)
				);
				
			$this->addLineFormat( 
					array( 
							"Purchase Order"	=> "L",
				            "Date"  			=> "L",
				            "Supplier"  		=> "L",
				            "Revision"  		=> "L",
				            "Your Order Number" => "L",
				            "Taken By"  		=> "L",
							"Cost"				=> "R"
					)
				);
			
			$this->SetY(36);
		}

		function __construct($orientation, $metric, $size, $dateFrom, $dateTo, $supplierid) {
			start_db();
			
			$this->dateFrom = $dateFrom;
			$this->dateTo = $dateTo;
			
			$dateFrom = convertStringToDate($dateFrom);
			$dateTo = convertStringToDate($dateTo);
				
	        parent::__construct($orientation, $metric, $size);
	        
	        $this->AddPage();
			
			try {
				$cumulativetotalcost = 0;
				$and = "";
				
				if ($supplierid != 0) {
					$and = "AND A.supplierid = $supplierid";
				}
				
				$sql = "SELECT A.*, DATE_FORMAT(A.orderdate, '%d/%m/%Y') AS orderdate,
						B.name AS suppliername,
						C.fullname
						FROM  {$_SESSION['DB_PREFIX']}proforma A
					    INNER JOIN  {$_SESSION['DB_PREFIX']}supplier B
					    ON B.id = A.supplierid
					    LEFT OUTER JOIN  {$_SESSION['DB_PREFIX']}members C
					    ON C.member_id = A.takenbyid
					    WHERE A.orderdate BETWEEN '$dateFrom' AND '$dateTo'
					    $and
					    ORDER BY B.name, A.id DESC";
				$result = mysql_query($sql);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($this->GetY() > 175) {
							$this->AddPage();
						}
						
						$cumulativetotalcost += $member['total'];
						
						$this->setY(
								$this->GetY() +
								$this->addLine(
										$this->getY(), 
										array( 
												"Purchase Order"	=> getPOReference($member['id']),
									            "Date"  			=> $member['orderdate'],
									            "Supplier"  		=> $member['suppliername'],
									            "Revision"  		=> $member['revision'],
									            "Your Order Number" => $member['yourordernumber'],
									            "Taken By"  		=> $member['fullname'],
									            "Cost"  			=> "£ " . $member['total']
										)
									)
							);
					}
					
					$this->SetFont('Arial','B', 6);
					$this->setY(
							$this->GetY() + 4 +
							$this->addLine(
									$this->getY() + 2, 
									array(
									        "Taken By"  => "Total Cost :",
									        "Cost"  => "£ " . number_format($cumulativetotalcost, 2)
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
	
	$pdf = new POReport( 
			'P', 
			'mm', 
			'A4', 
			$_POST['fromdate'], 
			$_POST['todate'],
			$_POST['supplierid']
		);
	$pdf->Output();
?>