<?php
require_once('system-db.php');
require_once('pdfreport.php');
require_once("simple_html_dom.php");

class InvoiceReport extends PDFReport {
	private $headermember = null;

	function newPage() {
		$this->AddPage();

		$this->DynamicImage(getSiteConfigData()->logoimageid, 220.6, 6, 55);

		$this->addText( 15, 13, getSiteConfigData()->companyname, 12, 4, 'B') + 5;
		$dynamicY = $this->addText(15, 20, getSiteConfigData()->address, 8, 3) + 4;

		$dynamicY = 42.5;

		$this->addText( 15, $dynamicY, "Customer Name & Address", 8, 3, 'B');
		$this->addText( 105, $dynamicY, "Delivery Address", 8, 3, 'B');
		$this->addText( 240, $dynamicY, "INVOICE", 8, 3, 'B');

		$this->addText( 215, $dynamicY + 5, "Invoice No:", 8, 3, 'B');
		$this->addText( 240, $dynamicY + 5, getInvoiceReference($this->headermember['id']), 8, 3, 'B');

		$this->addText( 215, $dynamicY + 10, "FAO:", 8, 3, 'B');
		$this->addText( 240, $dynamicY + 10, $this->headermember['contact2'], 8, 2.4, '', 30);

		$this->addText( 215, $dynamicY + 15, "Invoice Date:", 8, 3, 'B');
		$this->addText( 240, $dynamicY + 15, $this->headermember['orderdate'], 8, 3);

		$this->addText( 215, $dynamicY + 20, "Your Acc No:", 8, 3, 'B');
		$this->addText( 240, $dynamicY + 20, $this->headermember['accountcode'], 8, 3);

		$this->addText( 215, $dynamicY + 25, "Your Reference:", 8, 3, 'B');
		$this->addText( 240, $dynamicY + 25, $this->headermember['yourordernumber'], 8, 3);

		$this->addText( 215, $dynamicY + 31.5, "Due Date:", 8, 3, 'B');
		$this->addText( 240, $dynamicY + 31.5, $this->headermember['duedate'], 8, 3);
		
		
		$address = "";
		$address = "";

		if (trim($this->headermember['street']) != "") $address .= $this->headermember['street'] . "\n";
		if (trim($this->headermember['address2']) != "") $address .= $this->headermember['address2'] . "\n";
		if (trim($this->headermember['town']) != "") $address .= $this->headermember['town'] . "\n";
		if (trim($this->headermember['city']) != "") $address .= $this->headermember['city'] . "\n";
		if (trim($this->headermember['county']) != "") $address .= $this->headermember['county'] . "\n";
		if (trim($this->headermember['postcode']) != "") $address .= $this->headermember['postcode'] . "\n";
		if ($address == "") {
			$address = $address;
		}

		$this->addText(15, $dynamicY + 5, $this->headermember['customername'] . "\n" . $address, 8, 3.5, '', 60);
		$this->addText(105, $dynamicY + 5, $this->headermember['customername'] . "\n" . $address, 8, 3.5, '', 60);

		$this->RoundedRect(10, 41, 198, 38, 5, '1234', 'BD');
		$this->RoundedRect(213, 41, 73, 38, 5, '1234', 'BD');
		$this->Line(286, 46.5, 213, 46.5);
		$this->Line(286, 72, 213, 72);

		$this->addText( 10, 183, "Company Reg No: " . getSiteConfigData()->companynumber, 7, 3);
		$this->addText( 10, 186, "VAT Number: " . getSiteConfigData()->vatregnumber, 7, 3);
		$this->addText( 255, 183, "Printed: " . date("d/m/Y H:i"), 7, 3);
		$this->addText( 269, 186, "Page " . $this->PageNo() . " of {nb}", 7, 3);

		$this->SetFont('Arial','', 8);

		$cols=array( "Job"    => 24,
			"Date"  => 23,
			"Ref"  => 30,
			"Collection"  => 44.5,
			"Destination"  => 116.5,
			"Pallets"  => 19.5,	
			"Charge"  => 19.5
		);

		$this->addCols( $dynamicY + 45, $cols);

		$cols=array( "Job"    => "L",
			"Date"  => "L",
			"Ref"  => "L",
			"Collection"  => "L",
			"Destination"  => "L",
			"Pallets"  => "R",
			"Charge"  => "R"
		);
		$this->addLineFormat( $cols);
		
		$currentY = $this->GetY();
		
		return $currentY;
	}

	function __construct($orientation, $metric, $size, $id) {
		$dynamicY = 0;
		
		$this->bMargin = 0;

		start_db();

		parent::__construct($orientation, $metric, $size);

		try {
			$sql = "SELECT A.*, DATE_FORMAT(A.orderdate, '%d/%m/%Y') AS orderdate,
						B.name AS customername, B.accountcode, B.street, B.address2, B.town, 
						B.street, B.address2, DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL B.duedays DAY), '%d/%m/%Y') AS duedate,
						B.town, B.city, B.county, B.postcode, B.contact2,
						C.fullname AS takenbyname
					    FROM  {$_SESSION['DB_PREFIX']}invoice A
					    INNER JOIN  {$_SESSION['DB_PREFIX']}customer B
					    ON B.id = A.customerid
					    INNER JOIN  {$_SESSION['DB_PREFIX']}members C
					    ON C.member_id = A.takenbyid
					    WHERE A.id = $id
					    ORDER BY A.id DESC";
			$result = mysql_query($sql);

			if ($result) {
				while (($this->headermember = mysql_fetch_assoc($result))) {
					$discount = $this->headermember['discount'];
					$total = 0;
					$dynamicY = $this->newPage() + 7;

					$sql = "SELECT A.*, 
							B.ordernumber, B.ordernumber2, B.id AS bookingid, B.fromplace, B.legsummary, B.pallets,
							DATE_FORMAT(B.startdatetime, '%d/%m/%Y') AS bookingdate
							FROM {$_SESSION['DB_PREFIX']}invoiceitem A 
							INNER JOIN {$_SESSION['DB_PREFIX']}booking B 
							ON B.id = A.productid 
							WHERE A.invoiceid = $id 
							ORDER BY A.id";
					$itemresult = mysql_query($sql);

					if ($itemresult) {
						while (($itemmember = mysql_fetch_assoc($itemresult))) {
							$order = $itemmember['ordernumber'];
							
							if ($itemmember['ordernumber2'] != "" && $itemmember['ordernumber2'] != null) {
								$order .= "-" . $itemmember['ordernumber2'];
							}
							
							$line = array(
								"Job"    => getBookingReference($itemmember['bookingid']),
								"Date"  => $itemmember['bookingdate'],
								"Ref"  => $order,
								"Collection"  => $itemmember['fromplace'],
								"Destination"  => $itemmember['legsummary'],
								"Pallets"  => number_format($itemmember['pallets'], 2),
								"Charge"  => number_format($itemmember['linetotal'], 2)
							);

							$size = $this->addLine( $dynamicY, $line );
							$dynamicY += $size + 1;

							if ($dynamicY > 175) {
								$dynamicY = $this->newPage();
								$dynamicY = 96;
							}

							$total = $total + ($itemmember['linetotal']);

							$totalvat += $itemmember['vat'];
						}

					} else {
						logError($qry . " - " . mysql_error());
					}
					
					if ($dynamicY > 132) {
						$dynamicY = $this->newPage();
						$dynamicY = 96;
					}
					
					$queries = "ALL QUERIES AGAINST INVOICES TO BE NOTIFIED TO\n" . getSiteConfigData()->accountsemail . " WITHIN 5 DAYS OF INVOICE DATE";
			
					$y = $this->WriteHTML(135, 135, getSiteConfigData()->termsandconditions, 50) - 2;
					$y = $this->addText(135, $y, $queries, 8, 3.5, '', 110);
					$y = $this->addText(135, $y + 2, "Please make BACS transfer to the following account:" , 8, 3.5, 'B', 110);
			
					$this->addText(135, $y + 2, "Account Name", 8, 3.5, '', 60);
					$y = $this->addText(170, $y + 2, getSiteConfigData()->companyname, 8, 3.5, '', 110);
			
					$this->addText(135, $y, "Bank", 8, 3.5, '', 60);
					$y = $this->addText(170, $y, getSiteConfigData()->bank, 8, 3.5, '', 110);
			
					$this->addText(135, $y, "Sort Code", 8, 3.5, '', 60);
					$y = $this->addText(170, $y, getSiteConfigData()->banksortcode, 8, 3.5, '', 110);
			
					$this->addText(135, $y, "Account Number", 8, 3.5, '', 60);
					$y = $this->addText(170, $y, getSiteConfigData()->bankaccountnumber, 8, 3.5, '', 110);
					
					$line = array(
						"Job"    => " ",
						"Date"  => " ",
						"Ref"  => " " ,
						"Collection"  => " ",
						"Destination"  => " ",
						"Pallets"  => "Goods Net:",
						"Charge"  => number_format($total, 2)
					);

					$size = $this->addLine( 166, $line );

					$line = array(
						"Job"    => " ",
						"Date"  => " ",
						"Ref"  => " " ,
						"Collection"  => " ",
						"Destination"  => " ",
						"Pallets"  => "VAT:",
						"Charge"  => number_format($totalvat, 2)
					);

					$size = $this->addLine( 172, $line );

					$line = array(
						"Job"    => " ",
						"Date"  => " ",
						"Ref"  => " " ,
						"Collection"  => " ",
						"Destination"  => " ",
						"Pallets"  => "Total:",
						"Charge"  => number_format(($totalvat + $total) - $discount, 2)
					);

					$size = $this->addLine( 178, $line );
					$this->Line(248, 164, 287, 164);
					$this->Line(248, 170, 287, 170);
					$this->Line(248, 176, 287, 176);
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