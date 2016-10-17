<?php
	require_once('system-db.php');
	
	start_db();
	
	$customerid = $_POST['customerid'];
	$vehicletypeid = $_POST['vehicletypeid'];
	$pallets = $_POST['pallets'];
	$legs = $_POST['legs'];
	$where = "";
	$rows = 0;
	
	if ($customerid != 0) {
		$where .= "AND A.customerid = $customerid ";
	}
	
	if ($vehicletypeid != 0) {
		$where .= "AND A.vehicletypeid = $vehicletypeid ";
	}
	
	$sql = "SELECT A.id, A.pallets, A.charge, A.legsummary, B.name, C.name AS customername
			FROM {$_SESSION['DB_PREFIX']}booking A
			INNER JOIN {$_SESSION['DB_PREFIX']}vehicletype B
			ON B.id = A.vehicletypeid
			INNER JOIN {$_SESSION['DB_PREFIX']}customer C
			ON C.id = A.customerid
			WHERE 1 = 1
			$where";
	
	$result = mysql_query($sql);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			if ($rows++ == 0) {
				echo "<TABLE class='prevpricetable' cellspacing=0 cellpadding=5 border=1 width='100%'>
							<THEAD>
								<TR>
									<TD align='center'>Select</TD>
									<TD>Customer</TD>
									<TD>Vehicle Type</TD>
									<TD align='right'>Pallets</TD>
									<TD align='right'>Price</TD>
									<TD>Journey</TD>
								</TR>
							</THEAD>";
						}
			
			$id = $member['id'];
			$pallets = $member['pallets'];
			$customer = $member['customername'];
			$price = $member['charge'];
			$name = $member['name'];
			$journey = $member['legsummary'];
			
			echo 	"<TR>
						<TD align='center'>
							<INPUT type='radio' id='pricecheck' name='pricecheck' value='$price' />
						</TD>
						<TD>$customer</TD>
						<TD>$name</TD>
						<TD align='right'>$pallets</TD>
						<TD align='right'>$price</TD>
						<TD>$journey</TD>
					</TR>";
		}
	
	} else {
		logError("$sql - " . mysql_error(), false);
		
		echo mysql_errno();
		
		exit();
	}
	
	if ($rows == 0) {
		echo "<h4>No prices found</h4>";
		
	} else {
		echo "</TABLE>";
	}
?>
