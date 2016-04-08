<?php
	require_once('system-db.php');
	require_once('pdfreport.php');
	require_once("simple_html_dom.php");
	
	class RemittanceReport extends PDFReport {
		private $headermember = null;
		
		function newPage() {
			$this->AddPage();
			
			$this->Image("images/logomain2.png", 158.6, 6);
			
			$this->addText( 15, 13, "Allegro Transport Ltd", 12, 4, 'B') + 5;
			$dynamicY = $this->addText(15, 20, getSiteConfigData()->address, 8, 3) + 4;
			
			$dynamicY = 47.5;
			
			$this->addText( 15, $dynamicY, "Supplier Name & Address", 8, 3, 'B');
			$this->addText( 75, $dynamicY, "Remittance Address", 8, 3, 'B');
			$this->addText( 160, $dynamicY, "REMITTANCE ADVICE", 8, 3, 'B');
			
			$this->addText( 145, $dynamicY + 5, "FAO:", 8, 3, 'B');
			$this->addText( 170, $dynamicY + 5, $this->headermember['firstname'] . " " . $this->headermember['lastname'], 8, 2.4, '', 30);
			
			$this->addText( 145, $dynamicY + 10, "Remittance Date:", 8, 3, 'B');
			$this->addText( 170, $dynamicY + 10, $this->headermember['converteddatetime'], 8, 3);
			
			$this->addText( 145, $dynamicY + 15, "Your Acc No:", 8, 3, 'B');
			$this->addText( 170, $dynamicY + 15, $this->headermember['accountnumber'], 8, 3);
			
			$this->addText( 145, $dynamicY + 20, "Your Reference:", 8, 3, 'B');
			$this->addText( 170, $dynamicY + 20, $this->headermember['yourordernumber'], 8, 3);

			$this->addText( 145, $dynamicY + 25, "Taken By:", 8, 3, 'B');
			$this->addText( 170, $dynamicY + 25, $this->headermember['takenbyname'], 8, 3);

			$this->addText( 145, $dynamicY + 31.5, "Our Order No:", 8, 3, 'B');
			$this->addText( 170, $dynamicY + 31.5, getSiteConfigData()->bookingprefix . sprintf("%06d", $this->headermember['id'], 6), 8, 3, 'B');
			
			$invoiceaddress = "";
			$deliveryaddress = "";
			
			if (trim($this->headermember['deliveryaddress1']) != "") $deliveryaddress .= $this->headermember['deliveryaddress1'] . "\n";
			if (trim($this->headermember['deliveryaddress2']) != "") $deliveryaddress .= $this->headermember['deliveryaddress2'] . "\n";
			if (trim($this->headermember['deliveryaddress3']) != "") $deliveryaddress .= $this->headermember['deliveryaddress3'] . "\n";
			if (trim($this->headermember['deliverycity']) != "") $deliveryaddress .= $this->headermember['deliverycity'] . "\n";
			if (trim($this->headermember['deliverypostcode']) != "") $deliveryaddress .= $this->headermember['deliverypostcode'] . "\n";
			
			if (trim($this->headermember['invoiceaddress1']) != "") $invoiceaddress .= $this->headermember['invoiceaddress1'] . "\n";
			if (trim($this->headermember['invoiceaddress2']) != "") $invoiceaddress .= $this->headermember['invoiceaddress2'] . "\n";
			if (trim($this->headermember['invoiceaddress3']) != "") $invoiceaddress .= $this->headermember['invoiceaddress3'] . "\n";
			if (trim($this->headermember['invoicecity']) != "") $invoiceaddress .= $this->headermember['invoicecity'] . "\n";
			if (trim($this->headermember['invoicepostcode']) != "") $invoiceaddress .= $this->headermember['invoicepostcode'] . "\n";
			
			if ($deliveryaddress == "") {
				$deliveryaddress = $invoiceaddress;
			}
			
			$this->addText(15, $dynamicY + 5, $this->headermember['suppliername'] . "\n" . $invoiceaddress, 8, 3.5, '', 60);
			$this->addText(75, $dynamicY + 5, $this->headermember['suppliername'] . "\n" . $deliveryaddress, 8, 3.5, '', 60);
			
			$this->RoundedRect(13, 46, 128, 38, 5, '1234', 'BD');
			$this->RoundedRect(143, 46, 58, 38, 5, '1234', 'BD');
			$this->Line(143, 51.5, 201, 51.5);
			$this->Line(143, 77, 201, 77);
			$this->Line(142, 246, 200, 246);
			$this->Line(142, 252, 200, 252);
			$this->Line(142, 258, 200, 258);
			$this->Line(142, 264, 200, 264);

			$this->addText( 10, 270, "Company Reg No: 1202559", 7, 3);
			$this->addText( 170, 270, "Printed: " . date("d/m/Y H:i"), 7, 3);
			$this->addText( 186, 273, "Page " . $this->PageNo() . " of {nb}", 7, 3);
			
			$this->SetFont('Arial','', 8);
				
			$cols=array( "Cheque Number"    => 41,
						 "Description"  => 90.5,
			             "Price Each"  => 19.5,
						 "Line VAT"  => 19.5,
			             "Total Paid"  => 19.5
				);
		
			$this->addCols( $dynamicY + 45, $cols);
			
			$cols=array( "Cheque Number"    => "L",
						 "Description"  => "L",
			             "Price Each"  => "R",
						 "Line VAT"  => "R",
			             "Total Paid"  => "R"
				);
			$this->addLineFormat( $cols);
			
			return $this->GetY();
		}
		
		function __construct($orientation, $metric, $size, $id) {
			$dynamicY = 0;

			start_db();
				
	        parent::__construct($orientation, $metric, $size);
			
			try {
				$sql = "SELECT A.*, DATE_FORMAT(A.converteddatetime, '%d/%m/%Y') AS converteddatetime,
						B.name AS suppliername, B.accountnumber, B.invoiceaddress1, B.invoiceaddress2, B.invoiceaddress3, 
						B.invoicecity, B.invoicepostcode, B.deliveryaddress1, B.deliveryaddress2, 
						B.deliveryaddress3, B.deliverycity, B.deliverypostcode, B.firstname, B.lastname,
						C.fullname AS takenbyname
					    FROM  {$_SESSION['DB_PREFIX']}proforma A
					    INNER JOIN  {$_SESSION['DB_PREFIX']}supplier B
					    ON B.id = A.supplierid
					    INNER JOIN  {$_SESSION['DB_PREFIX']}members C
					    ON C.member_id = A.takenbyid
					    WHERE A.id = $id
					    ORDER BY A.id DESC";
				$result = mysql_query($sql);
				
				if ($result) {
					while (($this->headermember = mysql_fetch_assoc($result))) {
						$shipping = $this->headermember['deliverycharge'];
						$discount = $this->headermember['discount'];
						$total = 0;
						$dynamicY = $this->newPage() + 7;
						
						$sql = "SELECT A.*, B.productcode
								FROM {$_SESSION['DB_PREFIX']}proformaitem A 
								INNER JOIN {$_SESSION['DB_PREFIX']}product B 
								ON B.id = A.productid 
								WHERE A.proformaid = $id 
								ORDER BY A.id";
						$itemresult = mysql_query($sql);
						
						if ($itemresult) {
							while (($itemmember = mysql_fetch_assoc($itemresult))) {
								$line = array( 
										 "Cheque Number"    => "Paid by BACS",
										 "Description"  => "Remittance Advice Notice For : " . $this->headermember['yourordernumber'],
							             "Price Each"  => number_format($itemmember['priceeach'], 2),
										 "Line VAT"  => number_format($itemmember['vat'], 2),
							             "Total Paid"  => number_format($itemmember['linetotal'], 2)
							         );
								             
								$size = $this->addLine( $dynamicY, $line );
								$dynamicY += $size + 1;
								
								if ($dynamicY > 225) {
									$dynamicY = $this->newPage();
									$dynamicY = 102;
								}
			
								$total = $total + ($itemmember['priceeach'] * $itemmember['quantity']);

								$totalvat += $itemmember['vat'];
							}
							
						} else {
							logError($qry . " - " . mysql_error());
						}
						
						$line = array( 
								 "Cheque Number"    => " ",
								 "Description"  => " " ,
					             "Price Each"  => " ",
					             "Line VAT"  => "Goods Net:",
								 "Total Paid"  => number_format($total, 2)
					         );
						             
						$size = $this->addLine( 248, $line );
						
						$line = array( 
								 "Cheque Number"    => " ",
								 "Description"  => " " ,
					             "Price Each"  => " ",
					             "Line VAT"  => "VAT:",
								 "Total Paid"  => number_format($totalvat, 2)
					         );
						             
						$size = $this->addLine( 254, $line );
						
						$line = array( 
								 "Cheque Number"    => " ",
								 "Description"  => " " ,
					             "Price Each"  => " ",
					             "Line VAT"  => "Total:",
								 "Total Paid"  => number_format(($totalvat + $total) - $discount, 2)
					         );
						             
						$size = $this->addLine( 260, $line );
						

						$this->addText( 162, 265, "Pounds Sterling", 6, 3);
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
?>