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
			
			$this->addText( 10, 13, "Margins Report", 11, 'B');
			$this->addText( 10, 18, "For the period " . $this->dateFrom . " to " . $this->dateTo, 8, 'B');
			
		    $this->SetFont('Arial','', 6);
				
			$this->addCols(
					28, 
					array( 
							"Booking Number"	=> 19,
							"Invoice"	=> 17,
				            "Date"  			=> 16,
				            "Company Name"  	=> 46,
				            "Journey"  			=> 74,
				            "Pallets"  			=> 15,
				            "Weight (Kg)"  		=> 18,
				            "Revenue"  			=> 18,
							"Cost"				=> 18,
							"Profit"			=> 18,
							"Profit %"		    => 18
						)
				);
				
			$this->addLineFormat( 
					array( 
							"Booking Number"	=> "L",
							"Invoice"	=> "L",
				            "Date"  			=> "L",
				            "Company Name"  	=> "L",
				            "Journey"  			=> "L",
				            "Pallets"  			=> "R",
				            "Weight (Kg)"  		=> "R",
				            "Revenue"  			=> "R",
							"Cost"				=> "R",
							"Profit"			=> "R",
							"Profit %"		    => "R"
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
				$cumulativetotalcharge = 0;
				$cumulativetotalcost = 0;
				$cumulativetotalprofit = 0;
				$prevcustomername = "";
				
				$and = "";
				
				if ($customerid != 0) {
					$and = "AND A.customerid = $customerid";
				}
				
				$sql = "SELECT A.*, DATE_FORMAT(A.startdatetime, '%d/%m/%Y') AS startdatetime,
						B.name AS customername, C.invoiceid
						FROM  {$_SESSION['DB_PREFIX']}booking A
					    INNER JOIN  {$_SESSION['DB_PREFIX']}customer B
					    ON B.id = A.customerid
					    LEFT OUTER JOIN  {$_SESSION['DB_PREFIX']}invoiceitem C
					    ON C.productid = A.id
					    WHERE DATE(A.startdatetime) >= '$dateFrom'
					    AND DATE(A.startdatetime) <= '$dateTo'
					    AND A.statusid = 8
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
													"Revenue" => "£ " . number_format($cumulativetotalcharge, 2),
													"Cost" => "£ " . number_format($cumulativetotalcost, 2),
													"Profit" => "£ " . number_format($cumulativetotalprofit, 2),
											        "Profit %"  => number_format(100 - ($cumulativetotalcost * (100 / $cumulativetotalcharge)), 2) . " %"
									    	    )
										)
								);
				    		$this->SetFont('Arial','', 6);
						}
						
						if ($this->GetY() > 175) {
							$this->AddPage();
						}

						if ($member['invoiceid'] == null || $member['invoiceid'] == 0) {
							$invoice = "";

						} else {
							$invoice = getInvoiceReference($member['invoiceid']);
						}
						
						$this->setY(
								$this->GetY() +
								$this->addLine(
										$this->getY(), 
										array( 
												"Booking Number"	=> getBookingReference($member['id']),
												"Invoice"	=> $invoice,
									            "Date"  			=> $member['startdatetime'],
									            "Company Name"  	=> $member['customername'],
									            "Journey"  			=> $member['legsummary'],
									            "Pallets"  			=> $member['pallets'],
									            "Weight (Kg)"  		=> $member['weight'],
									            "Revenue"  			=> "£ " . $member['charge'],
									            "Cost"  			=> "£ " . $member['rate'],
									            "Profit"  			=> "£ " . number_format($member['charge'] - $member['rate'], 2),
										        "Profit %"  		=> number_format(100 - ($member['rate'] * (100 / $member['charge'])), 2) . " %"
										)
									)
							);
						             
						$prevcustomername = $member['customername'];
					    $cumulativetotalcharge += $member['charge'];
					    $cumulativetotalcost += $member['rate'];
						$cumulativetotalprofit += ($member['charge'] - $member['rate']);
					}
					
					if ($prevcustomername != "") {
						$this->Line(10, $this->GetY(), 287, $this->GetY());
					}
					
					$this->SetFont('Arial','B', 6);
					$this->setY(
							$this->GetY() + 4 +
							$this->addLine(
									$this->getY() + 2, 
									array(
											"Revenue" => "£ " . number_format($cumulativetotalcharge, 2),
											"Cost" => "£ " . number_format($cumulativetotalcost, 2),
											"Profit" => "£ " . number_format($cumulativetotalprofit, 2),
									        "Profit %"  => number_format(100 - ($cumulativetotalcost * (100 / $cumulativetotalcharge)), 2) . " %"
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