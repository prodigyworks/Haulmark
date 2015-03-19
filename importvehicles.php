<?php
	include("system-header.php"); 

	$monthnames = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

	function convertThisDate($dateString) {
	
		$day = substr($dateString, 0, 2);
		$month = substr($dateString, 3, 3);
		$year = "20" . substr($dateString, 7, 2);
		$monthnumber = "1";

		for ($i = 0; $i < count($monthnames); $i++) {
			if ($monthnames[$i] == $month) {
				$monthnumber = ($i + 1);
			}
			
		}

		if (strlen($monthnumber) == 1) {
			$monthnumber = "0" . $monthnumber;
		}

		return $year . "-" . $monthnumber . "-" . $day;

	}
	
	
	if (isset($_FILES['importfile']) && $_FILES['importfile']['tmp_name'] != "") {
		if ($_FILES["importfile"]["error"] > 0) {
			echo "Error: " . $_FILES["importfile"]["error"] . "<br />";
			
		} else {
		  	echo "Upload: " . $_FILES["importfile"]["name"] . "<br />";
		  	echo "Type: " . $_FILES["importfile"]["type"] . "<br />";
		  	echo "Size: " . ($_FILES["importfile"]["size"] / 1024) . " Kb<br />";
		  	echo "Stored in: " . $_FILES["importfile"]["tmp_name"] . "<br>";
		}
		
		$subcat1 = "";
		$row = 1;
		
		if (($handle = fopen($_FILES['importfile']['tmp_name'], "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		        if ($row++ == 1) {
		        	continue;
		        }
		        
		        $num = count($data);
		        $index = 0;
		        
		        $registration = mysql_escape_string($data[$index++]);
		        $description = mysql_escape_string($data[$index++]);
		        $purchasedate = convertThisDate(mysql_escape_string($data[$index++]));
                $manufacturer = mysql_escape_string($data[$index++]);
                $purchaseprice = str_replace("£", "", mysql_escape_string($data[$index++]));
                $presentvalue = str_replace("£", "", mysql_escape_string($data[$index++]));
				$weight = mysql_escape_string($data[$index++]);
				$type = mysql_escape_string($data[$index++]);
				$mileometer = mysql_escape_string($data[$index++]);
				$korm = mysql_escape_string($data[$index++]);
				$subcontractor = mysql_escape_string($data[$index++]) == "TRUE" ? "Y" : "N";
				$drivercode = mysql_escape_string($data[$index++]);
				$capacity = mysql_escape_string($data[$index++]);
				$mobileno = mysql_escape_string($data[$index++]);
				$trailer = mysql_escape_string($data[$index++]);
				$ourpercentage = mysql_escape_string($data[$index++]);
				$subcontractpercentage = mysql_escape_string($data[$index++]);
				$bookorder = mysql_escape_string($data[$index++]);
				$usualtrailer = mysql_escape_string($data[$index++]);
				$subconcode = mysql_escape_string($data[$index++]);
				$deleted = mysql_escape_string($data[$index++]);
				$mddepot = mysql_escape_string($data[$index++]);
				$rpomit = mysql_escape_string($data[$index++]);
				$alias = mysql_escape_string($data[$index++]);
				
		        if ($data[0] != "") {
		        	echo "<div>Unit: $description</div>";
	
					if ($trailer == "FALSE") {
						$vehicletypeid = 0;
						$usualdriverid = 0;
						$usualtrailerid = 0;
						
						$qry = "SELECT id FROM {$_SESSION['DB_PREFIX']}vehicletype WHERE code = '$type'";
						$result = mysql_query($qry);
						if (! $result) {
							logError($qry . " - " . mysql_error());
						}
					
						while (($member = mysql_fetch_assoc($result))) {
							$vehicletypeid = $member['id'];
						}
						
						$qry = "SELECT id FROM {$_SESSION['DB_PREFIX']}driver WHERE code = '$drivercode'";
						$result = mysql_query($qry);
						if (! $result) {
							logError($qry . " - " . mysql_error());
						}
					
						while (($member = mysql_fetch_assoc($result))) {
							$usualdriverid = $member['id'];
						}
						
						$qry = "SELECT id FROM {$_SESSION['DB_PREFIX']}trailer WHERE registration = '$usualtrailer'";
						$result = mysql_query($qry);
						if (! $result) {
							logError($qry . " - " . mysql_error());
						}
					
						while (($member = mysql_fetch_assoc($result))) {
							$usualtrailerid = $member['id'];
						}
						
						$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}vehicle 
								(
								registration, description, purchasedate,
								manufacturer, purchaseprice, presentprice,
								mpg, grossweight, type, notes, ystachometer, 
								vehicletypeid, capacity, usualdriverid, 
								usualtrailerid, mork,  mobileno,
								subcontractor
								)  
								VALUES  
								(
								'$registration', '$description', '$purchasedate',
								'$manufacturer', '$purchaseprice', $presentvalue,
								0, $weight, 'Y', '', $mileometer,
								$vehicletypeid, $capacity, $usualdriverid,
								$usualtrailerid, '$korm', '$mobileno', 
								'$subcontractor'
									
								)";
								
						$result = mysql_query($qry);
	        	
						if (mysql_errno() != 1062 && mysql_errno() != 0 ) {
							logError(mysql_error() . " : " .  $qry);
						}
	
					} else {
						$trailertypeid = 0;
						$usualdriverid = 0;
						$usualtrailerid = 0;
						
						$qry = "SELECT id FROM {$_SESSION['DB_PREFIX']}trailertype WHERE code = '$type'";
						$result = mysql_query($qry);
						if (! $result) {
							logError($qry . " - " . mysql_error());
						}
					
						while (($member = mysql_fetch_assoc($result))) {
							$trailertypeid = $member['id'];
						}
						
						$qry = "SELECT id FROM {$_SESSION['DB_PREFIX']}driver WHERE code = '$drivercode'";
						$result = mysql_query($qry);
						if (! $result) {
							logError($qry . " - " . mysql_error());
						}
					
						while (($member = mysql_fetch_assoc($result))) {
							$usualdriverid = $member['id'];
						}
						
						
						$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}trailer 
								(
								registration, description, purchasedate,
								manufacturer, purchaseprice, presentprice,
								mpg, grossweight, type, notes, ystachometer, 
								trailertypeid, capacity, usualdriverid
								)  
								VALUES  
								(
								'$registration', '$description', '$purchasedate',
								'$manufacturer', '$purchaseprice', $presentvalue,
								0, $weight, 'Y', '', $mileometer,
								$trailertypeid, $capacity, $usualdriverid
								)";
								
						$result = mysql_query($qry);
	        	
						if (mysql_errno() != 1062 && mysql_errno() != 0 ) {
							logError(mysql_error() . " : " .  $qry);
						}
					}
				}				
		    }
		    
		    fclose($handle);
			echo "<h1>" . $row . " downloaded</h1>";
		}
	}
	
	if (! isset($_FILES['importfile'])) {
?>	
		
<form class="contentform" method="post" enctype="multipart/form-data">
	<label>Upload vehicle CSV file </label>
	<br>
	<input type="file" name="importfile" id="importfile" /> 
	
	<br>
	<br />
	 	
	<div id="submit" class="show">
		<input type="submit" value="Upload" />
	</div>
</form>
<?php
	}
	
	include("system-footer.php"); 
?>